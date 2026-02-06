<?php

namespace Controllers\API;

use Helpers\PDF;
use Helpers\ZIP;
use Helpers\Date;
use Helpers\Form;
use Helpers\Excel;
use Helpers\Table;
use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Bike\Event;
use Database\Repository\Bike\Price;
use Database\Repository\User\Address;
use Database\Repository\Bike\Distance;
use Database\Repository\School\School;
use Database\Repository\Bike\DistanceType;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Database\Repository\Navigation\Setting;
use Database\Object\Bike\Event as ObjectBikeEvent;
use Database\Repository\User\User as RepositoryUser;
use Database\Object\Bike\Distance as ObjectBikeDistance;
use Database\Object\Export\Export as ExportExport;

class BikeController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "bike";

    // Get Functions
    protected function getHomeWork($view, $id = null)
    {
        $this->getEvent($view, $id, "HW");
    }

    protected function getWorkWork($view, $id = null)
    {
        $this->getEvent($view, $id, "WW");
    }

    protected function getDistance($view, $id)
    {
        $repo = new Distance;
        $currentUserId = User::getLoggedInUser()->id;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'userId' => $currentUserId,
                'type' => Helpers::url()->getParam("type")
            ];

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
        else if (Strings::equal($view, self::VIEW_LIST)) {
            $type = Helpers::input()->get('type')->getValue();
            $items = $repo->getByUserIdAndType($currentUserId, $type);
            $items = Arrays::map($items, fn($i) => $i->toArray(true));
            $this->appendToJson('raw', General::processTemplate($items));
        }
    }

    protected function getDistanceType($view, $id)
    {
        $repo = new DistanceType;

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', $repo->get());
        }
    }

    protected function getEvent($view, $id, $type)
    {
        $repo = new Event;
        $currentUserId = User::getLoggedInUser()->id;

        if (Strings::equal($view, self::VIEW_CALENDAR)) {
            $items = $repo->getByUserIdAndTypeDistanceMoreThenZero($currentUserId, $type);

            Arrays::each($items, fn($i) => $this->appendToJson(data: [
                "start" => $i->date,
                "title" => "{$i->alias} ({$i->formatted->distance})",
                "display" => "background",
                "classNames" => [
                    "bg-{$i->color}",
                    "text-{$i->textColor}"
                ],
                "allDay" => true,
            ]));
        }
    }

    protected function getSettings($view, $id = null)
    {
        return $this->getNavigationSettings();
    }

    // Post Functions
    protected function postHomeWork($view, $id = null)
    {
        $this->postEvent($view, $id, "HW");
    }
    protected function postWorkWork($view, $id = null)
    {
        $this->postEvent($view, $id, "WW");
    }

    protected function postDistance($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "alias" => ["mandatory" => true],
            "type" => ["mandatory" => true],
            "startId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "endSchoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "distance" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_FLOAT],
            "color" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Distance;

            foreach ($repo->getByUserId(User::getLoggedInUser()->id) as $_distance) {
                if (Strings::equal($_distance->id, $id) || Strings::equal($_distance->guid, $id)) continue;

                if (Strings::equal($_distance->alias, $fields['alias'])) {
                    $this->setValidation("alias", state: self::VALIDATION_STATE_INVALID);
                    $this->setToast("Er bestaat al een afstand met alias '{$fields['alias']}'!", self::VALIDATION_STATE_INVALID);
                }
                if (Strings::equal($_distance->type, $fields['type']) && Strings::equal($_distance->startId, $fields['startId']) && Strings::equal($_distance->endSchoolId, $fields['endSchoolId'])) {
                    $this->setValidation("startId", state: self::VALIDATION_STATE_INVALID);
                    $this->setToast("Er bestaat al een rit met hetzelfde startlocatie en school!", self::VALIDATION_STATE_INVALID);
                }
                if (!$this->validationIsAllGood()) break;
            }

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new ObjectBikeDistance;
                $item->fillWithPostData();
                $item->userId = User::getLoggedInUser()->id;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postEvent($view, $id, $type)
    {
        $settingsRepo = new Setting;

        $_fields = [
            "date" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if (General::convert($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.past.enabled")->value, 'bool')) {
            $pastDate = Clock::now()->toDateTime();
            if ($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.past.amount")->value !== 0) $pastDate->modify("-" . $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.past.amount")->value);
            $pastDate = Clock::at($pastDate->format('Y-m-d'));

            if ($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value && $pastDate->isBeforeOrEqualTo(Clock::at($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value))) $pastDate = Clock::at($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value);

            if (Clock::at($fields['date'])->isBefore($pastDate)) $this->setToast("U kan geen rit inboeken voor {$pastDate->format('d/m/Y')}", self::VALIDATION_STATE_INVALID);
        }

        if (General::convert($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.future.enabled")->value, 'bool')) {
            $futureDate = Clock::now()->toDateTime();
            if ($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.future.amount") !== 0) $futureDate->modify("+" . $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "block.future.amount")->value);
            $futureDate = Clock::at($futureDate->format('Y-m-d'));

            if (Clock::at($fields['date'])->isAfter($futureDate)) $this->setToast("U kan geen rit inboeken na {$futureDate->format('d/m/Y')}", self::VALIDATION_STATE_INVALID);
        }

        if ($this->validationIsAllGood()) {
            $rDate = Clock::at($fields['date'])->format("d/m/Y");

            $currentUserId = User::getLoggedInUser()->id;
            $repo = new Event;
            $dRepo = new Distance;

            $item = $repo->getByUserIdTypeAndDate($currentUserId, $type, $fields['date']) ?? new ObjectBikeEvent;
            $distances = $dRepo->getByUserIdAndType($currentUserId, $type);

            $distance = null;
            if ($item->bikeDistanceId == null) $distance = $distances[0];
            else {
                $break = false;
                foreach ($distances as $i => $d) {
                    if ($break) {
                        $distance = $d;
                        break;
                    }

                    if ($i + 1 == count($distances)) $distance = null;
                    else if ($d->id == $item->bikeDistanceId) $break = true;
                }
            }

            // $item->date = $date;
            $item->fillWithPostData();
            $item->bikeDistanceId = $distance->id;
            $item->type = $type;
            $item->userId = $currentUserId;
            $item->startId = $distance->startId;
            $item->endSchoolId = $distance->endSchoolId;
            $item->distance = $distance->distance;
            $item->alias = $distance->alias;
            $item->color = $distance->color;
            $item->userMainSchoolId = User::getLoggedInUser()->mainSchoolId;
            $item->pricePerKm = (new Price)->getBetween($item->date)->amount;
            $repo->set($item);

            if ($distance == null) $this->setToast("Rit op datum {$rDate} verwijderd!");
            else $this->setToast("Rit '{$distance->alias} ({$distance->formatted->distance})' op datum {$rDate} opgeslagen!");
            $this->setReloadCalendar();
        }
    }

    protected function postSettings($view, $id = null)
    {
        $this->postNavigationSettings();
    }

    protected function printExport($view, $id = null)
    {
        $_fields = [
            "type",
            "per",
            "school" => ["mandatory" => true],
            "start" => ["mandatory" => true],
            "end" => ["mandatory" => true]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if (is_array($fields['school']) && !Strings::contains($fields['school'], ";")) {
            $s = [];
            foreach ($fields['school'] as $sch)
                $s[] = $sch->getValue();

            $fields['school'] = $s;
        } else if (Strings::contains($fields['school'], ";")) {
            $fields['school'] = explode(";", $fields['school']);
        } else $fields['school'] = [$fields['school']];

        if ($this->validationIsAllGood()) {
            if (Strings::equal($fields['per'], "school")) $this->exportPerSchool($fields['school'], $fields['start'], $fields['end'], $fields['type']);
            else if (Strings::equal($fields['per'], "teacher")) $this->exportPerTeacher($fields['school'], $fields['start'], $fields['end'], $fields['type']);
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);

        $this->handle();
    }

    // Delete Functions
    protected function deleteDistance($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Distance;
        $bikeEventRepo = new Event;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            if (count($bikeEventRepo->getByBikeDistanceId($item->id))) {
                $this->setToast("De afstand '{$item->alias}' kan niet worden verwijderd!<br />Deze is gekoppeld aan ritten!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De afstand '{$item->alias}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    // Export functions
    private function exportPerSchool($schoolIds, $start, $end, $type)
    {
        $lastPayDate = (new Setting)->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value;
        $typeFull = (new DistanceType)->getById($type)->name;

        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/bike/" . date("YmdHis"));
        $filename = "Fietsvergoeding - Export Per School - {$typeFull}.xlsx";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");

        $startRow = 6;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");
        $excel->setSheetTitle(0, "Overzicht");
        $excel->setCellValue(0, "A1:P1", "Fietsvergoeding - Overzicht per school - {$typeFull}", true, 14);
        $excel->setCellValue(0, "A2", "Startdatum");
        $excel->setCellValue(0, "B2", Clock::at($start)->format("d/m/Y"));
        $excel->setCellValue(0, "A3", "Einddatum");
        $excel->setCellValue(0, "B3", Clock::at($end)->format("d/m/Y"));
        $excel->setCellValue(0, "A4", "Laatste uitbetalingsdatum");
        $excel->setCellValue(0, "B4", Clock::at($lastPayDate)->format("d/m/Y"));

        $overviewTable = [];
        $overviewTotalDistance = $overviewTotalPrice = 0;

        $overviewTable = [];
        $overviewTable["header"] = ["School"];

        foreach ($monthsBetweenDates as $month) $overviewTable["header"][] = $month;

        $overviewTable["header"][] = ["text" => "Totaal", "border" => "bl"];
        $overviewTable["header"][] = "";

        foreach ($schoolIds as $index => $schoolId) {
            $schoolTotalDistance = $schoolTotalPrice = 0;
            $schoolTotalDistancePerMonth = [];
            $groupedEvents = $this->getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end, $type);

            $school = $schoolRepo->getById($schoolId);
            $excel->createSheet($index + 1, $school->name);
            $excel->setCellValue($index + 1, "A1:P1", "Fietsvergoeding - {$school->name} - {$typeFull}", true, 14);
            $excel->setCellValue($index + 1, "A2", "Startdatum");
            $excel->setCellValue($index + 1, "B2", Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index + 1, "A3", "Einddatum");
            $excel->setCellValue($index + 1, "B3", Clock::at($end)->format("d/m/Y"));
            $excel->setCellValue($index + 1, "A4", "Laatste uitbetalingsdatum");
            $excel->setCellValue($index + 1, "B4", Clock::at($lastPayDate)->format("d/m/Y"));

            $schoolTable = [];
            $schoolTable["header"] = ["Leerkracht"];

            foreach ($monthsBetweenDates as $month) $schoolTable["header"][] = $month;

            $schoolTable["header"][] = ["text" => "Totaal", "border" => "bl"];
            $schoolTable["header"][] = "";

            $schoolTableRow = 0;
            foreach ($groupedEvents as $user => $events) {
                $userTotalDistance = $userTotalPrice = 0;

                $schoolTable["data"][$schoolTableRow][] = $user;

                foreach ($monthsBetweenDates as $month) {
                    $schoolTable["data"][$schoolTableRow][] = number_format(($events[$month]['distance'] ?? 0), 2, ",", ".") . " km";
                    $userTotalDistance += $events[$month]['distance'] ?? 0;
                    $schoolTotalDistancePerMonth[$month] += $events[$month]['distance'] ?? 0;
                    $userTotalPrice += $events[$month]['price'] ?? 0;
                }

                $schoolTable["data"][$schoolTableRow][] = ["text" => number_format($userTotalDistance, 2, ",", ".") . " km", "border" => "l"];
                $schoolTable["data"][$schoolTableRow][] = "€ " . number_format($userTotalPrice, 2, ",", ".");

                $schoolTotalDistance += $userTotalDistance;
                $schoolTotalPrice += $userTotalPrice;
                $schoolTableRow++;
            }

            $schoolTable["data"][$schoolTableRow][] = "";
            foreach ($monthsBetweenDates as $month) $schoolTable["data"][$schoolTableRow][] = "";
            $schoolTable["data"][$schoolTableRow][] = ["text" => number_format($schoolTotalDistance, 2, ",", ".") . " km", "border" => "t", "borderStyle" => Border::BORDER_DOUBLE];
            $schoolTable["data"][$schoolTableRow][] = ["text" => "€ " . number_format($schoolTotalPrice, 2, ",", "."), "border" => "t", "borderStyle" => Border::BORDER_DOUBLE];

            $overviewTable["data"][$index][] = ["text" => $school->name, "link" => "sheet://'{$school->name}'!A1"];
            foreach ($monthsBetweenDates as $month) $overviewTable["data"][$index][] = number_format(($schoolTotalDistancePerMonth[$month] ?? 0), 2, ",", ".") . " km";
            $overviewTable["data"][$index][] = ["text" => number_format(($schoolTotalDistance ?? 0), 2, ",", ".") . " km", "border" => "l"];
            $overviewTable["data"][$index][] = "€ " .  number_format(($schoolTotalPrice ?? 0), 2, ",", ".");

            $excel->table($index + 1, $startColumn, $startRow, $schoolTable);

            $overviewTotalDistance += $schoolTotalDistance;
            $overviewTotalPrice += $schoolTotalPrice;
        }

        $overviewTable["data"][count($schoolIds)][] = "";
        foreach ($monthsBetweenDates as $month) $overviewTable["data"][count($schoolIds)][] = "";
        $overviewTable["data"][count($schoolIds)][] = ["text" => number_format(($overviewTotalDistance ?? 0), 2, ",", ".") . " km", "border" => "tl", "borderStyle" => Border::BORDER_DOUBLE];
        $overviewTable["data"][count($schoolIds)][] = ["text" => "€ " .  number_format(($overviewTotalPrice ?? 0), 2, ",", "."), "border" => "t", "borderStyle" => Border::BORDER_DOUBLE];

        $excel->table(0, $startColumn, $startRow, $overviewTable);
        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    private function exportPerTeacher($schoolIds, $start, $end, $type)
    {
        $lastPayDate = (new Setting)->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "lastPayDate")->value;
        $typeFull = (new DistanceType)->getById($type)->name;

        $folder = FileSystem::CreateFolder(LOCATION_FILES . "/bike/" . date("YmdHis"));
        $filename = "Fietsvergoeding - Export Per Leerkracht - {$typeFull}.xlsx";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");

        $excel = new Excel("{$folder}/{$filename}");
        $groupedEvents = $this->getEventsGroupedByMonthByTeacherBySchool($start, $end, $schoolIds, $type);

        $startRow = 9;
        $startColumn = "A";

        foreach ($groupedEvents as $username => $userEvent) {
            $row = 0;
            $table = [];

            $userTotalSingle = $userTotalDouble = $userTotalPrice = 0;

            $index = array_search($username, array_keys($groupedEvents));
            $user = $userEvent['user'];
            $address = $userEvent['address'];
            $events = $userEvent['events'];

            if ($index == 0) $excel->setSheetTitle($index, $user->formatted->fullNameReversed);
            else $excel->createSheet($index, $user->formatted->fullNameReversed);

            $excel->setCellValue($index, "A1:E1", "Fietsvergoeding - Overzicht - {$typeFull}: {$user->formatted->fullNameReversed}", true, 14);
            $excel->setCellValue($index, "A2", "Hoofdschool");
            $excel->setCellValue($index, "B2:E2", $user->linked->mainSchool->name);
            $excel->setCellValue($index, "A3", "Huidig adres");
            $excel->setCellValue($index, "B3:E3", $address->formatted->address);
            $excel->setCellValue($index, "A4", "Rekeningnummer");
            $excel->setCellValue($index, "B4:E4", $user->bankAccount);
            $excel->setCellValue($index, "A5", "Startdatum");
            $excel->setCellValue($index, "B5:E5", Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index, "A6", "Einddatum");
            $excel->setCellValue($index, "B6:E6", Clock::at($end)->format("d/m/Y"));
            $excel->setCellValue($index, "A7", "Laatste uitbetalingsdatum");
            $excel->setCellValue($index, "B7:E7", Clock::at($lastPayDate)->format("d/m/Y"));

            $table["header"] = [
                "Datum",
                "Afstand - Enkel",
                "Afstand - Dubbel",
                "Vergoeding/km",
                "Vergoeding totaal"
            ];

            foreach ($monthsBetweenDates as $month) {
                $monthTotalSingle = $monthTotalDouble = $monthTotalPrice = 0;

                $table["data"][$row][] = ["text" => $month, "bold" => true];
                $row++;

                foreach ($events[$month] as $event) {
                    $table["data"][$row] = [
                        Clock::at($event->date)->format("d/m/Y"),
                        number_format($event->distance, 2, ",", ".") . " km",
                        number_format($event->distance * 2, 2, ",", ".") . " km",
                        "€ " . number_format($event->pricePerKm, 2, ",", "."),
                        "€ " . number_format($event->pricePerKm * ($event->distance * 2), 2, ",", ".")
                    ];

                    $monthTotalSingle += $event->distance;
                    $monthTotalDouble += $event->distance * 2;
                    $monthTotalPrice += $event->pricePerKm * ($event->distance * 2);
                    $row++;
                }

                $table["data"][$row] = [
                    [
                        "text" => count($events[$month] ?? []) . " rit(ten)",
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ],
                    [
                        "text" => number_format($monthTotalSingle, 2, ",", ".") . " km",
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ],
                    [
                        "text" => number_format($monthTotalDouble, 2, ",", ".") . " km",
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ],
                    [
                        "text" => "",
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ],
                    [
                        "text" => "€ " . number_format($monthTotalPrice, 2, ",", "."),
                        "border" => "t",
                        "borderStyle" => Border::BORDER_DOUBLE
                    ]
                ];

                $row++;
                $table["data"][$row] = [];

                $userTotalSingle += $monthTotalSingle;
                $userTotalDouble += $monthTotalDouble;
                $userTotalPrice += $monthTotalPrice;
                $row++;
            }

            $table["data"][$row] = [
                [
                    "text" => "Totaal",
                    "bold" => true
                ],
                [
                    "text" => number_format($userTotalSingle, 2, ",", ".") . " km",
                    "bold" => true
                ],
                [
                    "text" => number_format($userTotalDouble, 2, ",", ".") . " km",
                    "bold" => true
                ],
                [
                    "text" => "",
                    "bold" => true
                ],
                [
                    "text" => "€ " . number_format($userTotalPrice, 2, ",", "."),
                    "bold" => true
                ],
            ];

            $excel->table($index, $startColumn, $startRow, $table);
        }

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    // Other functions
    protected function getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end, $type)
    {
        $eventRepo = new Event;
        $userRepo = new RepositoryUser();
        $eventsGrouped = [];

        $events = $eventRepo->getByUserMainSchoolIdAndType($schoolId, $type);
        $events = Arrays::filter($events, fn($e) => Clock::at($e->date)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->date)->isBeforeOrEqualTo(Clock::at($end)));

        Arrays::each($events, fn($e) => $e->user = $userRepo->getById($e->userId)->formatted->fullNameReversed);
        $events = Arrays::filter($events, fn($e) => !is_null($e->distance) && !Strings::equal($e->distance, 0));
        $events = Arrays::orderBy($events, "user");

        foreach ($events as $event) {
            $eventsGrouped[$event->user][Clock::at($event->date)->format("F Y")]['distance'] += floatval($event->distance) * 2;
            $eventsGrouped[$event->user][Clock::at($event->date)->format("F Y")]['price'] += floatval($event->pricePerKm) * (floatval($event->distance) * 2);
        }

        return $eventsGrouped;
    }

    protected function getEventsGroupedByMonthByTeacherBySchool($start, $end, $allowedSchoolIds, $type)
    {
        $eventRepo = new Event;
        $userRepo = new RepositoryUser;
        $userAddressRepo = new Address;
        $eventsGrouped = [];

        $users = Arrays::orderBy($userRepo->get(), "name");

        foreach ($users as $user) {
            if (Strings::isBlank($user->username)) continue;
            $eventsGrouped[$user->username]['user'] = $user;

            if (!Arrays::contains($allowedSchoolIds, $user->mainSchoolId)) {
                unset($eventsGrouped[$user->username]);
                continue;
            }

            $address = $userAddressRepo->getCurrentByUserId($user->id);
            $eventsGrouped[$user->username]['address'] = $address;

            $events = $eventRepo->getByUserIdAndTypeDistanceMoreThenZeroBetweenDates($user->id, $type, $start, $end);

            $events = Arrays::filter($events, fn($e) => Clock::at($e->date)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->date)->isBeforeOrEqualTo(Clock::at($end)));
            $events = Arrays::filter($events, fn($e) => !is_null($e->distance) && !Strings::equal($e->distance, 0));

            if (!count($events)) {
                unset($eventsGrouped[$user->username]);
                continue;
            }

            $events = Arrays::orderBy($events, "date");

            foreach ($events as $event) {
                $eventsGrouped[$user->username]['events'][Clock::at($event->date)->format("F Y")][] = $event;
            }
        }

        return $eventsGrouped;
    }
}
