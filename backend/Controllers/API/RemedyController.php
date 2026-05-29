<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Object\Navigation\TableDef;
use Database\Object\Remedy\Moment as RemedyMoment;
use Database\Object\Remedy\Remedy as RemedyRemedy;
use Database\Object\Remedy\Type as RemedyType;
use Database\Object\Smartschool\Message as SmartschoolMessage;
use Database\Object\Smartschool\MessageReceiver as SmartschoolMessageReceiver;
use Database\Repository\General\Schoolyear;
use Database\Repository\Holliday;
use Database\Repository\Informat\Employee;
use Database\Repository\Remedy\ComputerType;
use Database\Repository\Remedy\Moment;
use Database\Repository\Remedy\Remedy;
use Database\Repository\Remedy\Type;
use Database\Repository\School\CourseInformatEmployeeClassgroup;
use Database\Repository\School\CourseInformatEmployeeStudentClassgroup;
use Database\Repository\School\Department;
use Database\Repository\Smartschool\Message;
use Database\Repository\Smartschool\MessageReceiver;
use Database\Repository\Sync\Sync;
use Database\Repository\User\User as UserUser;
use Helpers\Date;
use Helpers\Excel;
use Helpers\Filter;
use Helpers\Form;
use Helpers\General;
use Helpers\Table;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\FileSystem;
use Security\Input;
use Security\User;

class RemedyController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "remedy";

    // Get Functions
    protected function getType($view, $id = null)
    {
        $repo = new Type;
        $filters = Filter::Find(['schoolId', 'departmentId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);

            if (Helpers::url()->hasParam("show") && Strings::equal(Helpers::url()->getParam("show"), "limit")) {
                $items = Arrays::filter($items, fn($i) => is_null($i->from) || Clock::now()->isAfterOrEqualTo(Clock::at($i->from)));
                $items = Arrays::filter($items, fn($i) => is_null($i->until) || Clock::now()->isBeforeOrEqualTo(Clock::at($i->until)));
                $items = array_values($items);
            }

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getComputerType($view, $id = null)
    {
        $repo = new ComputerType;
        $filters = Filter::Find([]);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getMoment($view, $id = null)
    {
        $repo = new Moment;
        $remedyRepo = new Remedy;
        $filters = Filter::Find(['schoolId', 'departmentId', 'typeId', 'courseId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $items = Arrays::filter($items, fn($i) => !$i->full);
            Arrays::each($items, fn($i) => $i->formatted->seats = $i->seats == 0 ? $i->formatted->seats : ($i->linked->type->manualAssignDate ? $i->formatted->seats : count($remedyRepo->getByMomentId($i->id)) . "/{$i->formatted->seats}"));
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $items = Arrays::filter($items, fn($i) => !$i->full);
            $items = Arrays::filter($items, fn($i) => $i->linked->type->manualAssignDate || Clock::at($i->date . " " . $i->linked->hour->start)->isAfterOrEqualTo(Clock::at(date("Y-m-d H:i:s", strtotime("next " . WEEK_DAYS['en'][$i->linked->type->closeRegistrationAt] . " 8:59:00")))));
            $items = Arrays::filter($items, fn($i) => !$i->isPast);

            $items = array_values($items);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getMine($view, $id = null)
    {
        $repo = new Remedy;
        $filters = Filter::Find(['schoolId', 'departmentId', 'typeId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        }
    }

    protected function getCourse($view, $id = null)
    {
        $repo = new Moment;
        $filters = Filter::Find(['typeId']);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $items = Arrays::map($items, fn($i) => $i->linked->course);
            if (!Arrays::find($items, fn($i) => !is_null($i))) $items = [];
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getCoursePerStudent($view, $id = null)
    {
        $repo = new CourseInformatEmployeeStudentClassgroup;

        $filters = Filter::Find(['informatStudentId', 'informatClassgroupId']);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            if (empty(Arrays::getNestedValue($filters, ['informatStudentId', 0]))) {
                $this->appendToJson('items', []);
                return;
            }

            $items = $repo->get(filters: $filters);
            Arrays::each($items, fn($i) => $i->id = "{$i->schoolCourseId}-{$i->informatEmployeeId}-{$i->informatStudentId}-{$i->informatClassgroupId}");
            Arrays::each($items, fn($i) => $i->name = "{$i->linked->schoolCourse->name} - {$i->linked->informatEmployee->formatted->fullNameReversed}");
            $items = Arrays::orderBy($items, "name");
            $items = Arrays::uniqueBy($items, fn($i) => $i->name);
            $items = array_values($items);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getAssign($view, $id = null)
    {
        $momentRepo = new Moment;
        $remedyRepo = new Remedy;
        $hollidayRepo = new Holliday;
        $currentUserId = User::getLoggedInUser()->id;

        if (Strings::equal($view, self::VIEW_CALENDAR)) {
            $items = $momentRepo->getByUserId($currentUserId);
            $items = Arrays::filter($items, fn($i) => $i->linked->type->manualAssignDate);

            Arrays::each($items, function ($i) use ($hollidayRepo, $remedyRepo) {
                $startDate = $i->linked->type->from ?: $i->linked->schoolyear->start;
                $endDate = $i->linked->type->until ?: $i->linked->schoolyear->end;

                $date = Clock::at(date("Y-m-d", strtotime("next " . WEEK_DAYS['en'][$i->dayOfWeek], strtotime($startDate))));

                while ($date->isBefore(Clock::at($endDate))) {
                    $holliday = $hollidayRepo->dateContainsHolliday($date->format("Y-m-d"));

                    if (!$holliday) {
                        $this->appendToJson(data: [
                            "start" => $date->format("Y-m-d"),
                            "title" => "{$i->linked->type->name} {$i->linked->course->name} ({$i->linked->hour->formatted->startEnd})",
                            "display" => "background",
                            "classNames" => [
                                "bg-{$i->linked->type->color}" . ($date->isBefore(Clock::now()) ? "-lt" : ""),
                                "text-white"
                            ],
                            "allDay" => true,
                        ]);

                        $assignedStudents = $remedyRepo->getByAssignedDate($date->format("Y-m-d"));

                        foreach ($assignedStudents as $assignedStudent) {
                            $this->appendToJson(data: [
                                "id" => $assignedStudent->id,
                                "start" => $date->format("Y-m-d"),
                                "title" => "{$assignedStudent->linked->informatStudent->formatted->fullNameReversed} ({$assignedStudent->linked->classgroup->name})",
                                "color" => "lime" . ($date->isBefore(Clock::now()) ? "-lt" : ""),
                                "allDay" => true,
                                "disabled" => ($date->isBefore(Clock::now()))
                            ]);
                        };
                    }

                    $date = $date->plusDays(7);
                }
            });
        }
    }

    protected function getNotAssigned($view, $id = null)
    {
        $repo = new Remedy;
        $momentRepo = new Moment;
        $currentUserId = User::getLoggedInUser()->id;

        if (Strings::equal($view, self::VIEW_LIST)) {
            $items = $momentRepo->getByUserId($currentUserId);
            $items = Arrays::filter($items, fn($i) => $i->linked->type->manualAssignDate);
            $students = [];

            foreach ($items as $i) {
                $_students = $repo->getByMomentId($i->id);
                $_students = Arrays::filter($_students, fn($i) => is_null($i->assignedDate));
                $_students = Arrays::map($_students, fn($i) => $i->toArray(true));
                $students = Arrays::concat([$students, $_students]);
            }

            $this->appendToJson('raw', General::processTemplate($students));
        }
    }

    protected function getPresence($view, $id = null)
    {
        $remedyRepo = new Remedy;
        $currentUserId = User::getLoggedInUser()->id;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $remedyRepo->get();
            $items = Arrays::filter($items, fn($i) => Strings::equal($i->linked->moment->userId, $currentUserId));
            $items = Arrays::filter($items, fn($i) => $i->linked->type->manualAssignDate);
            $items = Arrays::filter($items, fn($i) => !is_null($i->assignedDate));
            // $items = Arrays::filter($items, fn($i) => Clock::at($i->assignedDate . " 23:59:59")->isBeforeOrEqualTo(Clock::at(Clock::nowAsString("Y-m-d 23:59:59"))));
            $items = Arrays::uniqueBy($items, fn($i) => $i->assignedDate);
            $items = Arrays::uniqueBy($items, fn($i) => $i->momentId);

            $items = array_values($items);
            foreach ($items as $i) {
                $seats = count($remedyRepo->getByMomentIdAndAssignedDate($i->momentId, $i->assignedDate));
                $i->formatted->seats = "{$seats}/{$i->linked->moment->seats}" . ($seats == $i->linked->moment->seats ? " (vol)" : "");
            }
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_LIST)) {
            $item = $remedyRepo->getById($id);
            $seats = $item->linked->type->manualAssignDate ? $remedyRepo->getByMomentIdAndAssignedDate($item->momentId, $item->assignedDate) : $remedyRepo->getByMomentId($item->momentId);

            $data = [
                [
                    "title" => "School",
                    "content" => $item->linked->school->formatted->badge->name
                ],
                [
                    "title" => "Type",
                    "content" => $item->linked->type->name
                ],
                [
                    "title" => "Datum",
                    "content" => $item->formatted->date->display
                ],
                [
                    "title" => "Lesuur",
                    "content" => $item->linked->moment->linked->hour->formatted->startEnd
                ],
                [
                    "title" => "Vak",
                    "content" => $item->linked->course->name,
                ],
                [
                    "title" => "Lokaal",
                    "content" => $item->linked->moment->linked->room->formatted->buildingRoom
                ],
                [
                    "title" => "Omschrijving",
                    "content" => $item->linked->moment->description
                ]
            ];

            $this->appendToJson('raw', General::processTemplate($data, searchPrePost: "#"));
        }
    }

    protected function getPresenceStudents($view, $id)
    {
        $repo = new Remedy;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format([
                new TableDef([
                    "order" => 1,
                    "title" => "Status",
                    "data" => "formatted.badge.status",
                    "orderable" => false,
                    "searchable" => false,
                    "width" => 20
                ]),
                new TableDef([
                    "order" => 2,
                    "title" => "Klas",
                    "data" => "linked.classgroup.name",
                    "orderable" => false,
                    "searchable" => false,
                    "width" => 100
                ]),
                new TableDef([
                    "order" => 3,
                    "title" => "Naam",
                    "data" => "linked.informatStudent.formatted.fullNameReversed",
                    "orderable" => false,
                    "searchable" => false,
                    "width" => 0,
                    "defaultOrder" => true,
                    "defaultOrderOrder" => 1,
                    "defaultOrderDirection" => "asc"
                ])
            ]);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $item = $repo->getById($id);
            $items = $repo->getByMomentIdAndAssignedDate($item->momentId, $item->assignedDate);
            $this->appendToJson("rows", $items);
        }
    }

    protected function getOverview($view, $id = null)
    {
        $remedyRepo = new Remedy;
        $filters = Filter::Find(['schoolId', 'departmentId', 'typeId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $remedyRepo->get(filters: $filters);
            $items = Arrays::uniqueBy($items, fn($i) => $i->assignedDate ?: $i->linked->moment->date);

            $items = array_values($items);
            Arrays::each($items, fn($i) => $i->formatted->seats = count($i->linked->type->manualAssignDate ? $remedyRepo->getByMomentIdAndAssignedDate($i->momentId, $i->assignedDate) : $remedyRepo->getByMomentId($i->momentId)) . "/{$i->linked->moment->formatted->seats}");
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_LIST)) {
            $item = $remedyRepo->getById($id);
            $seats = $item->linked->type->manualAssignDate ? $remedyRepo->getByMomentIdAndAssignedDate($item->momentId, $item->assignedDate) : $remedyRepo->getByMomentId($item->momentId);

            $data = [
                [
                    "title" => "School",
                    "content" => $item->linked->school->formatted->badge->name
                ],
                [
                    "title" => "Type",
                    "content" => $item->linked->type->name
                ],
                [
                    "title" => "Datum",
                    "content" => $item->formatted->date->display
                ],
                [
                    "title" => "Lesuur",
                    "content" => $item->linked->moment->linked->hour->formatted->startEnd
                ],
                [
                    "title" => "Vak",
                    "content" => $item->linked->course->name,
                ],
                [
                    "title" => "Lokaal",
                    "content" => $item->linked->moment->linked->room->formatted->buildingRoom
                ],
                [
                    "title" => "Aantal plaatsen",
                    "content" => $item->linked->moment->seats == 0 ? $item->linked->moment->formatted->seats : count($seats) . "/{$item->linked->moment->formatted->seats}"
                ],
                [
                    "title" => "Omschrijving",
                    "content" => $item->linked->moment->description
                ]
            ];

            $this->appendToJson('raw', General::processTemplate($data, searchPrePost: "#"));
        }
    }

    protected function getOverviewStudents($view, $id)
    {
        $repo = new Remedy;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $item = $repo->getById($id);

            $tabledef = [];

            if ($item->linked->type->manualAssignDate) $tabledef[] = new TableDef([
                "order" => 1,
                "title" => "Status",
                "data" => "formatted.badge.status",
                "orderable" => false,
                "searchable" => false,
                "width" => 20,
            ]);

            $tabledef[] = new TableDef([
                "order" => 2,
                "title" => "Klas",
                "data" => "linked.classgroup.name",
                "orderable" => false,
                "searchable" => false,
                "width" => 100
            ]);
            $tabledef[] = new TableDef([
                "order" => 3,
                "title" => "Naam",
                "data" => "linked.informatStudent.formatted.fullNameReversed",
                "orderable" => false,
                "searchable" => false,
                "width" => 300,
                "defaultOrder" => true,
                "defaultOrderOrder" => 1,
                "defaultOrderDirection" => "asc"
            ]);

            if ($item->linked->type->courseDependsOnSkore) {
                $tabledef[] = new TableDef([
                    "order" => 4,
                    "title" => "Vak",
                    "data" => "linked.course.name",
                    "orderable" => false,
                    "searchable" => false,
                    "width" => 300,
                ]);
                $tabledef[] = new TableDef([
                    "order" => 5,
                    "title" => "Leerkracht",
                    "data" => "linked.informatEmployee.formatted.fullNameReversed",
                    "orderable" => false,
                    "searchable" => false,
                    "width" => 300,
                ]);
            }

            $tabledef[] = new TableDef([
                "order" => 6,
                "title" => "Opmerking",
                "data" => "remark",
                "orderable" => false,
                "searchable" => false,
                "width" => 0,
            ]);

            [$defaultOrder, $columns] = Table::Format($tabledef, false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $courseTeacherRepo = new CourseInformatEmployeeStudentClassgroup;
            $items = $item->linked->type->manualAssignDate ? $repo->getByMomentIdAndAssignedDate($item->momentId, $item->assignedDate) : $repo->getByMomentId($item->momentId);
            Arrays::each($items, fn($i) => $i->linked->informatEmployee = $courseTeacherRepo->getBySchoolCourseIdInformatStudentIdAndInformatClassgroupId($i->schoolId, $i->informatStudentId, $i->classgroupId)->linked->informatEmployee);
            $this->appendToJson("rows", $items);
        }
    }

    // Post functions
    protected function postType($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "departmentId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "courseDependsOnSkore"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Type;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new RemedyType;
                $item->fillWithPostData();
                $item->schoolId = (new Department)->getById($fields['departmentId'])->schoolId;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postMoment($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["type" => Input::INPUT_TYPE_INT],
            "schoolyearId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "departmentId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "typeId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "courseId" => ['type' => Input::INPUT_TYPE_INT],
            "buildingId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "informatEmployeeId" => ["type" => Input::INPUT_TYPE_INT],
            "roomId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "seats" => ['type' => Input::INPUT_TYPE_INT],
            "description",
            "repeat" => ["default" => null],
            "repeatAt" => ["mandatory" => true, "preconditions" => ["date" => null, "repeat" => "W"]],
            "dayOfWeek" => ["type" => Input::INPUT_TYPE_INT],
            "hourId" => ["mandatory" => true, "preconditions" => ["date" => null], 'type' => Input::INPUT_TYPE_INT],
            "startDate" => ['default' => null],
            "endDate" => ["default" => null],
            "date" => ["default" => null]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $manualAssignDate = (new Type)->getById($fields['typeId'])->manualAssignDate;
            $repo = new Moment;
            $userRepo = new UserUser;
            $informatEmployeeRepo = new Employee;

            if ($this->validationIsAllGood()) {
                $informatEmployee = $informatEmployeeRepo->getById($fields['informatEmployeeId']);
                $user = $userRepo->getByInformatEmployeeId($informatEmployee->informatId) ?? $userRepo->getByInformatEmployeeId("P{$informatEmployee->informatId}");

                if (is_null($fields['repeat']) || Strings::equal($fields['repeat'], "N")) {
                    $item = $repo->getById($id) ?? new RemedyMoment;
                    $item->fillWithPostData();
                    $item->userId = $user->id;
                    if (!$fields['date']) $item->date = $fields['startDate'];

                    if ($manualAssignDate) {
                        $item->dayOfWeek = $id ? $fields['dayOfWeek'] : Date::stringToDayOfWeek($fields['repeatAt'], "en");
                        $item->date = null;
                    }

                    $repo->set($item);
                } else {
                    $schoolyear = (new Schoolyear)->getById($fields['schoolyearId']);

                    if (!$fields['startDate']) $fields['startDate'] = Clock::now()->isAfterOrEqualTo(Clock::at($schoolyear->start)) ? Clock::nowAsString("Y-m-d") : $schoolyear->start;
                    if (!$fields['endDate']) $fields['endDate'] = $schoolyear->end;

                    $date = Clock::at($fields['startDate']);
                    $date = Clock::at(date("Y-m-d", strtotime("next {$fields['repeatAt']}", $date->format("U"))));

                    $hollidayRepo = new Holliday;
                    while ($date->isBefore(Clock::at($fields['endDate']))) {
                        $holliday = $hollidayRepo->dateContainsHolliday($date->format("Y-m-d"));

                        if (!$holliday) {
                            $item = $repo->getById($id) ?? new RemedyMoment;
                            $item->fillWithPostData();
                            $item->date = $date->format("Y-m-d");
                            $item->userId = $user->id;

                            if ($manualAssignDate) {
                                $item->dayOfWeek = $id ? $fields['dayOfWeek'] : Date::stringToDayOfWeek($fields['repeatAt'], "en");
                                $item->date = null;
                            }

                            $repo->set($item);
                        }

                        $date = $date->plusDays(7);
                    }
                }
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postMine($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "departmentId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "typeId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "courseId" => ['type' => Input::INPUT_TYPE_INT],
            "momentId" => ["type" => Input::INPUT_TYPE_INT],
            "informatStudentId" => ["mandatory" => true],
            "informatClassgroupId",
            "coursePerStudent",
            "computerType",
            "computerTypeOther" => ["mandatory" => true, "preconditions" => ["computerType" => "O"]],
            "computerPassword",
            "remark" => ["type" => Input::INPUT_TYPE_STRING],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        $repo = new Remedy;
        $department = (new Department)->getById($fields['departmentId']);
        $type = (new Type)->getById($fields['typeId']);
        $moment = (new Moment)->getById($fields['momentId']);

        $syncRepo = new Sync;
        $userRepo = new UserUser;
        $smsMessageRepo = new Message;
        $smsMessageReceiverRepo = new MessageReceiver;

        if (!$type->manualAssignDate && $moment->seats != 0) {
            $currentSeats = count($repo->getByMomentId($fields['momentId']));
            $newSeats = count(explode(";", $fields['informatStudentId']));
            $seats = $newSeats + $currentSeats;
            $availableSeats = $moment->seats - $currentSeats;
            if ($seats >= $moment->seats) $this->setToast("Helaas zijn er nog {$availableSeats} plaatsen vrij, u probeert er {$newSeats} in te schrijven...", self::VALIDATION_STATE_INVALID);
        }

        if ($this->validationIsAllGood()) {
            $user = User::getLoggedInUser();
            [$schoolCourseId, $informatEmployeeId, $informatStudentId, $informatClassgroupId] = explode("-", $fields['coursePerStudent']);

            $smartschoolSourceId = $department->linked->school->smartschoolSourceId ?? $department->linked->school->linked->parentSchool->smartschoolSourceId;

            $title = "";
            $body = "";

            if ($type->manualAssignDate) {
                $title = "Er zijn nieuwe leerlingen ingeschreven voor uw {$type->name}";
                $body = "
                Beste {$moment->linked->informatEmployee->formatted->fullNameReversed}, <br />
                <br />
                <p>Onderstaande leerlingen werden door {$user->formatted->fullNameReversed} <a href=\"https://intranet.kaboe.be/remedy/presence.php\" target=\"_blank\">ingeschreven via intranet</a> om een {$type->name} bij u te volgen" . ($fields['remark'] ? " met volgende opmerking" : "") . ":</p>";
                if ($fields['remark']) $body .= "<p>{$fields['remark']}</p>";

                $body .= "
                <br />
                <table style=\"padding: 10px; border: 1px solid black;\">
                <thead>
                    <tr>
                        <td style=\"border: solid 1px black; width: 300px; padding: 10px;\">Naam</td>
                        <td style=\"border: solid 1px black; width: 50px; padding: 10px;\">Klas</td>
                    </tr>
                </thead>
                <tbody>
                ";
            }

            foreach (explode(";", $fields['informatStudentId']) as $index => $_informatStudentId) {
                $item = new RemedyRemedy;
                $item->fillWithPostData($fields);
                $item->creatorUserId = User::getLoggedInUser()->id;
                $item->informatStudentId = $_informatStudentId;;
                if (!$type->manualAssignDate) $item->courseId = $schoolCourseId;
                $item->classgroupId = !$type->manualAssignDate ? $informatClassgroupId : (explode(";", $fields['informatClassgroupId'])[$index] ?? null);

                $item = $repo->getById($repo->set($item));

                if ($item->linked->type->manualAssignDate) {
                    $body .= "
                <tr>
                    <td style=\"border: solid 1px black; width: 300px; padding: 10px;\">{$item->linked->informatStudent->formatted->fullNameReversed}</td>
                    <td style=\"border: solid 1px black; width: 50px; padding: 10px;\">{$item->linked->classgroup->name}</td>
                </tr>
                ";
                } else {
                    $title = "{$item->linked->type->name} voor {$item->linked->course->name}";
                    $body = "
                    Beste {$item->linked->informatStudent->formatted->fullNameReversed}, beste ouders,<br />
                    <br />
                    Je bent ingeschreven voor de {$item->linked->type->name} van {$item->linked->course->name} op {$item->linked->moment->formatted->date->display} {$item->linked->moment->linked->hour->formatted->start} in lokaal {$item->linked->moment->linked->room->formatted->full}.<br />
                    Gelieve dit te noteren in je agenda en hiervoor aanwezig te zijn a.u.b.
                    ";

                    $message = new SmartschoolMessage;
                    $message->sourceId = $smartschoolSourceId;
                    $message->subject = Strings::trimToNull($title);
                    $message->body = Strings::trimToNull($body);

                    $messageId = $smsMessageRepo->set($message);

                    for ($i = 0; $i <= 2; $i++) {
                        $receiver = new SmartschoolMessageReceiver;
                        $receiver->messageId = $messageId;
                        $receiver->username = DEV_MODE ? DEV_CONTACT : $syncRepo->getByEmployeeId($item->informatStudentId)->setEmail;
                        $receiver->account = $i;

                        $smsMessageReceiverRepo->set($receiver);
                    }
                }
            }

            if ($type->manualAssignDate) {
                $body .= "</tbody></table><br /><p>Gelieve de leerlingen zo snel mogelijk een plaatsje te geven.</p>";
                $message = new SmartschoolMessage;
                $message->sourceId = $smartschoolSourceId;
                $message->subject = Strings::trimToNull($title);
                $message->body = Strings::trimToNull($body);

                $messageId = $smsMessageRepo->set($message);

                $receiver = new SmartschoolMessageReceiver;
                $receiver->messageId = $messageId;
                $receiver->username = DEV_MODE ? DEV_CONTACT : $userRepo->getByInformatEmployeeId($moment->linked->informatEmployee->informatId)->username;

                $smsMessageReceiverRepo->set($receiver);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postAssign($view, $id = null)
    {
        $repo = new Remedy;
        $smsMessageRepo = new Message;
        $smsMessageReceiverRepo = new MessageReceiver;

        $_fields = [
            "date" => ["mandatory" => true, "preconditions" => ["remove" => false]],
            "id" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "remove" => ["mandatory" => false, 'type' => Input::INPUT_TYPE_BOOL, "default" => false]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($fields['id']);

            if (!$fields['remove'] && Clock::at($fields['date'])->isBeforeOrEqualTo(Clock::now())) $this->setToast("Je kan niet inplannen voor of op vandaag.", self::VALIDATION_STATE_INVALID);
            else if (!$fields['remove'] && !Strings::equal(Clock::at($fields['date'])->format("w"), $item->linked->moment->dayOfWeek)) $this->setToast("Er staat op die dag geen {$item->linked->type->name} gepland!", self::VALIDATION_STATE_INVALID);
            else {
                $origDate = $item->assignedDate;
                $item->assignedDate = $fields['date'];

                if (!$fields['remove'] && $item->linked->moment->seats != 0 && $item->linked->type->manualAssignDate) {
                    $currentSeats = count($repo->getByAssignedDate($item->assignedDate));
                    $seats = $currentSeats + 1;
                    $availableSeats = $item->linked->moment->seats - $currentSeats;
                    if ($seats > $item->linked->moment->seats) $this->setToast("Helaas zijn er nog maar {$availableSeats} plaatsen vrij, er zijn {$item->linked->moment->seats} plaatsen in totaal...", self::VALIDATION_STATE_INVALID);
                }

                if ($this->validationIsAllGood()) {
                    $repo->set($item);

                    $smartschoolSourceId = $item->linked->department->linked->school->smartschoolSourceId ?? $item->linked->department->linked->school->linked->parentSchool->smartschoolSourceId;

                    $subject = "";
                    $body = "";

                    if ($fields['remove']) {
                        $this->setToast("{$item->linked->informatStudent->formatted->fullNameReversed} is verwijderd van de {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($origDate)->format("d/m/Y") . "!");

                        $subject = "Uw {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($origDate)->format("d/m/Y") . " is geannuleerd!";
                        $body = "
                        Beste {$item->linked->informatStudent->formatted->fullNameReversed}, beste ouder,<br />
                        <br />
                        Je bent uitgeschreven voor de {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($origDate)->format("d/m/Y") . ".<br />
                        Je mag dit item dus schrappen uit je agenda.
                    ";
                    } else if (!Strings::equal($origDate ?: $fields['date'], $fields['date'])) {
                        $this->setToast("De {$item->linked->type->name} voor {$item->linked->course->name} voor {$item->linked->informatStudent->formatted->fullNameReversed} is verplaatst naar " . Clock::at($fields['date'])->format("d/m/Y") . "!");

                        $subject = "Uw {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($origDate)->format("d/m/Y") . " is verplaatst naar " . Clock::at($fields['date'])->format("d/m/Y") . "!";
                        $body = "
                        Beste {$item->linked->informatStudent->formatted->fullNameReversed}, beste ouder,<br />
                        <br />
                        De {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($origDate)->format("d/m/Y") . " is verplaatst.<br />
                        Deze gaat nu door op " . Clock::at($fields['date'])->format("d/m/Y") . " tijdens lesuur {$item->linked->moment->linked->hour->formatted->startEnd} in lokaal {$item->linked->moment->linked->room->formatted->buildingRoom}.<br />
                        Gelieve deze aanpassing te noteren in je agenda!<br />
                        <br />
                        Gelieve hiervoor stipt aanwezig te zijn a.u.b.<br />
                        Ziekte of afwezigheid meld je steeds via een Smartschoolbericht aan je vakleerkracht.
                    ";
                    } else {
                        $this->setToast("{$item->linked->informatStudent->formatted->fullNameReversed} is ingeschreven voor de {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($fields['date'])->format("d/m/Y") . "!");

                        $subject = "U bent ingeschreven voor een {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($fields['date'])->format("d/m/Y");
                        $body = "
                        Beste {$item->linked->informatStudent->formatted->fullNameReversed}, beste ouder,<br />
                        <br />
                        Je bent ingeschreven voor de {$item->linked->type->name} voor {$item->linked->course->name}.<br />
                        Deze gaat door op " . Clock::at($fields['date'])->format("d/m/Y") . " tijdens lesuur {$item->linked->moment->linked->hour->formatted->startEnd} in lokaal {$item->linked->moment->linked->room->formatted->buildingRoom}.<br />
                        Gelieve deze te noteren in je agenda a.u.b.<br />
                        <br />
                        Gelieve hiervoor stipt aanwezig te zijn a.u.b.<br />
                        Ziekte of afwezigheid meld je steeds via een Smartschoolbericht aan je vakleerkracht.
                    ";
                    }

                    $message = new SmartschoolMessage;
                    $message->sourceId = $smartschoolSourceId;
                    $message->subject = Strings::trimToNull($subject);
                    $message->body = Strings::trimToNull($body);

                    $messageId = $smsMessageRepo->set($message);

                    $syncRepo = new Sync;
                    for ($i = 0; $i <= 2; $i++) {
                        $receiver = new SmartschoolMessageReceiver;
                        $receiver->messageId = $messageId;
                        $receiver->username = DEV_MODE ? DEV_CONTACT : $syncRepo->getByEmployeeId($item->informatStudentId)->setEmail;
                        $receiver->account = $i;

                        $smsMessageReceiverRepo->set($receiver);
                    }
                }

                $this->setReloadList();
            }

            $this->setReloadCalendar();
        }
    }

    protected function postPresencePresent($view, $id = null, $present = true)
    {
        $this->postPresence($view, $id, $present);
    }

    protected function postPresenceNotPresent($view, $id = null, $present = false)
    {
        $this->postPresence($view, $id, $present);
    }

    protected function postPresence($view, $id = null, $present)
    {
        $repo = new Remedy;
        $id = explode("_", $id);
        $smsMessageRepo = new Message;
        $smsMessageReceiverRepo = new MessageReceiver;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->present = $present;
            $repo->set($item);

            if (!$present) {
                $smartschoolSourceId = $item->linked->department->linked->school->smartschoolSourceId ?? $item->linked->department->linked->school->linked->parentSchool->smartschoolSourceId;

                $subject = "Afwezig bij {$item->linked->type->name} voor {$item->linked->course->name}";
                $body = "
                Beste {$item->linked->informatStudent->formatted->fullNameReversed}, beste ouders,<br />
                <br />
                Je was afwezig tijdens de {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($item->assignedDate)->format("d/m/Y") . " tijdens lesuur {$item->linked->moment->linked->hour->formatted->startEnd} in lokaal {$item->linked->moment->linked->room->formatted->buildingRoom}.<br />
                Indien je vergeten bent om je afwezigheid te melden aan de vakleerkracht, denk je hieraan in de toekomst?
                ";

                $message = new SmartschoolMessage;
                $message->sourceId = $smartschoolSourceId;
                $message->subject = Strings::trimToNull($subject);
                $message->body = Strings::trimToNull($body);

                $messageId = $smsMessageRepo->set($message);

                $syncRepo = new Sync;
                for ($i = 0; $i <= 2; $i++) {
                    $receiver = new SmartschoolMessageReceiver;
                    $receiver->messageId = $messageId;
                    $receiver->username = DEV_MODE ? DEV_CONTACT : $syncRepo->getByEmployeeId($item->informatStudentId)->setEmail;
                    $receiver->account = $i;

                    $smsMessageReceiverRepo->set($receiver);
                }

                $subject = "Afwezigheid van {$item->linked->informatStudent->formatted->fullNameReversed} voor de {$item->linked->type->name} van {$item->linked->course->name}";
                $body = "
                Beste {$item->linked->creatorUser->formatted->fullNameReversed},<br />
                <br />
                {$item->linked->informatStudent->formatted->fullNameReversed} was afwezig tijdens de {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($item->assignedDate)->format("d/m/Y") . " tijdens lesuur {$item->linked->moment->linked->hour->formatted->startEnd} in lokaal {$item->linked->moment->linked->room->formatted->buildingRoom} waarvoor u hem/haar heeft ingeschreven.<br />
                Volgt u dit verder op?
                ";

                $message = new SmartschoolMessage;
                $message->sourceId = $smartschoolSourceId;
                $message->subject = Strings::trimToNull($subject);
                $message->body = Strings::trimToNull($body);

                $messageId = $smsMessageRepo->set($message);

                $receiver = new SmartschoolMessageReceiver;
                $receiver->messageId = $messageId;
                $receiver->username = DEV_MODE ? DEV_CONTACT : $item->linked->creatorUser->username;

                $smsMessageReceiverRepo->set($receiver);
            }
        }

        $this->setReloadTable();
    }

    // Delete functions    
    protected function deleteType($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Type;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count((new Remedy)->getByTypeId($item->id))) $attachtedTo[] = "remediëringen";
            if (count((new Moment)->getByTypeId($item->id))) $attachtedTo[] = "inhaalmomenten";

            if (count($attachtedTo)) {
                $this->setToast("Het type '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het type '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteMoment($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Moment;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            if (count((new Remedy)->getByTypeId($item->id))) $attachtedTo[] = "remediëringen";

            if (count($attachtedTo)) {
                $this->setToast("Het inhaalmoment '{$item->formatted->shortDescription}' kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het inhaalmoment '{$item->formatted->shortDescription}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteMine($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Remedy;
        $smsMessageRepo = new Message;
        $smsMessageReceiverRepo = new MessageReceiver;
        $syncRepo = new Sync;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $attachtedTo = [];

            // if (count((new Remedy)->getByTypeId($item->id))) $attachtedTo[] = "remediëringen";

            if (count($attachtedTo)) {
                $this->setToast("De registratie kan niet worden verwijderd!<br />Deze is gekoppeld aan " . join(", ", $attachtedTo) . "!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $subject = "Uw {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($item->assignedDate)->format("d/m/Y") . " is geannuleerd!";
            $body = "
                Beste {$item->linked->informatStudent->formatted->fullNameReversed}, beste ouder,<br />
                <br />
                Je bent uitgeschreven voor de {$item->linked->type->name} voor {$item->linked->course->name} op " . Clock::at($item->assignedDate)->format("d/m/Y") . ".<br />
                Je mag dit item dus schrappen uit je agenda.
            ";

            $smartschoolSourceId = $item->linked->department->linked->school->smartschoolSourceId ?? $item->linked->department->linked->school->linked->parentSchool->smartschoolSourceId;

            $message = new SmartschoolMessage;
            $message->sourceId = $smartschoolSourceId;
            $message->subject = Strings::trimToNull($subject);
            $message->body = Strings::trimToNull($body);

            $messageId = $smsMessageRepo->set($message);

            for ($i = 0; $i <= 2; $i++) {
                $receiver = new SmartschoolMessageReceiver;
                $receiver->messageId = $messageId;
                $receiver->username = DEV_MODE ? DEV_CONTACT : $syncRepo->getByEmployeeId($item->informatStudentId)->setEmail;
                $receiver->account = $i;

                $smsMessageReceiverRepo->set($receiver);
            }

            $this->setToast("De registratie '{$item->formatted->shortDescription}' is verwijderd en iedereen is op de hoogte gebracht!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function printExport($view, $id = null)
    {
        $_fields = [
            "schoolId" => ["mandatory" => true],
            "departmentId" => ["mandatory" => true],
            "typeId" => ["mandatory" => true],
            "start" => ["default" => null],
            "end" => ["default" => null]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if (is_array($fields['typeId']) && !Strings::contains($fields['typeId'], ";")) {
            $s = [];
            foreach ($fields['typeId'] as $sch)
                $s[] = $sch->getValue();

            $fields['typeId'] = $s;
        } else if (Strings::contains($fields['typeId'], ";")) {
            $fields['typeId'] = explode(";", $fields['typeId']);
        } else $fields['typeId'] = [$fields['typeId']];

        if ($this->validationIsAllGood()) {
            if ($fields['start']) $fields['start'] .= " 00:00:00";
            if ($fields['end']) $fields['end'] .= " 23:59:59";

            $this->export($fields['schoolId'], $fields['departmentId'], $fields['typeId'], $fields['start'], $fields['end']);
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Export functions
    private function export($schoolId, $departmentId, $typeIds, $start, $end)
    {
        $typeRepo = new Type();
        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/remedy/" . date("YmdHis"));
        $filename = "Remediëring.xlsx";

        $items = $this->getAllGrouped($schoolId, $departmentId, $typeIds, $start, $end);

        $startRow = 5;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");

        foreach ($typeIds as $index => $typeId) {
            $type = $typeRepo->getById($typeId);

            if ($index == 0) $excel->setSheetTitle($index, $type->name);
            else $excel->createSheet($index, $type->name);

            $excel->setCellValue($index, "A1:P1", "Remediëring - {$type->name}", true, 14);
            $excel->setCellValue($index, "A2", "Startdatum");
            $excel->setCellValue($index, "B2", is_null(Strings::trimToNull($start)) ? "" : Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index, "A3", "Einddatum");
            $excel->setCellValue($index, "B3", is_null(Strings::trimToNull($end)) ? "" : Clock::at($end)->format("d/m/Y"));

            $table = [];
            $table["header"] = [];

            $table["header"][] = "Datum";
            $table["header"][] = "Lesuur";
            $table["header"][] = "Lokaal";
            if (!$type->courseDependsOnSkore) $table["header"][] = "Vak";
            if ($type->manualAssignDate) $table["header"][] = "Status";
            $table["header"][] = "School";
            $table["header"][] = "Afdeling";
            $table["header"][] = "Klas";
            $table["header"][] = "Naam";

            if ($type->courseDependsOnSkore) {
                $table["header"][] = "Vak";
                $table["header"][] = "Leerkracht";
            }
            $table["header"][] = "Opmerking";

            foreach ($items[$typeId] as $i => $item) {
                $table["data"][$i] = [];

                $table["data"][$i][] = Clock::at($type->manualAssignDate ? $item->assignedDate : $item->linked->moment->date)->format("d/m/Y");
                $table["data"][$i][] = $item->linked->moment->linked->hour->formatted->startEnd;
                $table["data"][$i][] = $item->linked->moment->linked->room->formatted->buildingRoom;
                if (!$type->courseDependsOnSkore) $table["data"][$i][] = $item->linked->course->name;
                if ($type->manualAssignDate) $table["data"][$i][] = $item->formatted->status;
                $table["data"][$i][] = $item->linked->school->name;
                $table["data"][$i][] = $item->linked->department->name;
                $table["data"][$i][] = $item->linked->classgroup->name;
                $table["data"][$i][] = $item->linked->informatStudent->formatted->fullNameReversed;

                if ($type->courseDependsOnSkore) {
                    $table["data"][$i][] = $item->linked->course->name;
                    $table["data"][$i][] = $item->linked->informatEmployee->formatted->fullNameReversed;
                }

                $table["data"][$i][] = $item->remark;
            }

            $excel->table($index, $startColumn, $startRow, $table);
        }

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    protected function getAllGrouped($schoolId, $departmentId, $typeIds, $start, $end)
    {
        $return = [];
        $repo = new Remedy;
        $courseTeacherRepo = new CourseInformatEmployeeStudentClassgroup;

        $items = $repo->get();
        $items = Arrays::filter($items, fn($i) => Strings::equal($i->schoolId, $schoolId));
        $items = Arrays::filter($items, fn($i) => Strings::equal($i->departmentId, $departmentId));
        Arrays::each($items, fn($i) => $i->linked->informatEmployee = $courseTeacherRepo->getBySchoolCourseIdInformatStudentIdAndInformatClassgroupId($i->schoolId, $i->informatStudentId, $i->classgroupId)->linked->informatEmployee);

        if ($start) $items = Arrays::filter($items, fn($i) => Clock::at($i->assignedDate ?: $i->linked->moment->date)->isAfterOrEqualTo(Clock::at($start)));
        if ($end) $items = Arrays::filter($items, fn($i) => Clock::at($i->assignedDate ?: $i->linked->moment->date)->isBeforeOrEqualTo(Clock::at($end)));

        foreach ($typeIds as $typeId) $return[$typeId] = Arrays::filter($items, fn($i) => Strings::equal($i->typeId, $typeId));

        return $return;
    }
}
