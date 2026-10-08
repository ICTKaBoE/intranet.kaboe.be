<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\General\Schoolyear;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Informat\Student;
use Database\Repository\Informat\StudentAddress;
use Database\Repository\Informat\StudentBank;
use Database\Repository\Informat\StudentConfig;
use Database\Repository\Informat\StudentEmail;
use Database\Repository\Informat\StudentNumber;
use Database\Repository\Informat\StudentRelation;
use Database\Repository\School\Institute;
use Database\Repository\School\School;
use Database\Repository\Sync\Sync;
use Helpers\Filter;
use Helpers\Form;
use Helpers\General;
use Helpers\PDF;
use Helpers\Table;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\FileSystem;
use Security\Input;
use Security\User;

class StudentController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "student";

    // GET
    protected function getOverview($view, $id = null)
    {
        $repo = new Student;
        $configRepo = new StudentConfig;
        $regRepo = new Registration;
        $regClassRepo = new RegistrationClass;
        $classRepo = new ClassGroup;
        $schoolRepo = new School;
        $instituteRepo = new Institute;

        $addressRepo = new StudentAddress;
        $relationRepo = new StudentRelation;
        $numberRepo = new StudentNumber;
        $emailRepo = new StudentEmail;
        $bankRepo = new StudentBank;

        $filters = Filter::Find(['schoolyearId', 'schoolId']);
        if (empty($filters['schoolyearId'])) $filters['schoolyearId'] = [(new Schoolyear)->getCurrent()->id];
        // if (Helpers::url()->hasParam('schoolId')) $filters["instituteId"] = Arrays::map($instituteRepo->getBySchoolId(Helpers::url()->getParam("schoolId")), fn($i) => $i->id);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $configRepo->get(filters: $filters);

            // $schoolyear = Helpers::url()->hasParam('schoolyearId') ? (new Schoolyear)->getById(Helpers::url()->getParam('schoolyearId')) : (new Schoolyear)->getCurrent();
            // $items = $repo->get(filters: $filters);

            // foreach ($items as $index => $i) {
            //     $currentRegistration = $regRepo->getCurrentByInformatStudentId($i->id);

            //     if (!$currentRegistration) {
            //         unset($items[$index]);
            //         continue;
            //     }

            //     $currentRegistrationClass = $regClassRepo->getCurrentByInformatRegistrationId($currentRegistration->id);
            //     if (!$currentRegistrationClass) continue;

            //     $i->linked->registration = $currentRegistrationClass;
            //     $i->linked->class = $classRepo->getById($currentRegistrationClass->informatClassGroupId);
            // }

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_LIST) && $id) {
            $config = $configRepo->getById($id);
            $item = $repo->getById($config->informatStudentId);
            if (!$item) return;

            $addresses = $addressRepo->getByInformatStudentId($config->informatStudentId);
            $addresses = Arrays::map($addresses, fn($a) => $a->formatted->full);
            $addresses = implode("<br />", $addresses);

            $relations = $relationRepo->getByInformatStudentId($config->informatStudentId);
            $relations = Arrays::map($relations, fn($r) => $r->formatted->typeWithFullNameReversed);
            $relations = implode("<br />", $relations);

            $numbers = $numberRepo->getByInformatStudentId($config->informatStudentId);
            $numbers = Arrays::map($numbers, fn($n) => $n->formatted->typeWithLink);
            $numbers = implode("<br />", $numbers);

            $emails = $emailRepo->getByInformatStudentId($config->informatStudentId);
            $emails = Arrays::map($emails, fn($e) => $e->formatted->typeWithLink);
            $emails = implode("<br />", $emails);

            $banks = $bankRepo->getByInformatStudentId($config->informatStudentId);
            $banks = Arrays::map($banks, fn($b) => $b->formatted->details);
            $banks = implode("<br />", $banks);

            $registrations = $regRepo->getByInformatStudentId($config->informatStudentId);
            $registrations = Arrays::filter($registrations, fn($cr) => $cr->status == 0);
            $registrations = array_reverse(Arrays::orderBy($registrations, "start"));

            $history = "";
            foreach ($registrations as $registration) {
                $school = $schoolRepo->getById($instituteRepo->getById($registration->schoolInstituteId)->schoolId);
                $classRegistrations = $regClassRepo->getByInformatRegistrationId($registration->id);
                $classRegistrations = array_reverse(Arrays::orderBy($classRegistrations, "start"));

                $history .= "{$school->name} (Stamnummer {$registration->basenumber}) - {$registration->formatted->dates}";
                $history .= "<ul>";

                foreach ($classRegistrations as $cr) {
                    $classgroup = $classRepo->getById($cr->informatClassGroupId);
                    $history .= "<li>{$classgroup->code} (n°{$cr->rank}) - {$cr->formatted->dates}</li>";
                }

                $history .= "</ul>";
            }

            $items = [
                [
                    "title" => "Naam",
                    "content" => $config->linked->informatStudent->name
                ],
                [
                    "title" => "Voornaam",
                    "content" => $config->linked->informatStudent->firstName
                ],
                [
                    "title" => "Intern nummer",
                    "content" => $config->linked->informatStudent->informatId
                ],
                [
                    "title" => "Geboortedatum",
                    "content" => $config->linked->informatStudent->formatted->birthDate
                ],
                [
                    "title" => "Geboorteplaats",
                    "content" => $config->linked->informatStudent->birthPlace
                ],
                [
                    "address" => $addresses,
                    "relation" => $relations,
                    "number" => $numbers,
                    "email" => $emails,
                    "bank" => $banks,
                    "history" => $history,
                    "informatGuid" => $config->linked->informatStudent->informatGuid,
                    "fullNameReversed" => $config->linked->informatStudent->formatted->fullNameReversed
                ]
            ];

            $this->appendToJson('raw', General::processTemplate($items, searchPrePost: "#"));
        }
    }

    // POST
    protected function postOverviewChangePassword($view, $id = null)
    {
        $informatStudentRepo = new Student;
        $repo = new Sync;
        $id = explode("_", $id);

        $_fields = [
            "random" => ["convert" => "bool"],
            "password" => ["type" => "string", "mandatory" => true, "preconditions" => ["random" => false]]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            foreach ($id as $_id) {
                $item = $repo->getByEmployeeId($informatStudentRepo->getById($_id)->informatId);

                if (!is_null($item->action) && $item->action !== "U") {
                    $this->setToast("Kan het wachtwoord van '{$item->linked->employee->formatted->fullNameReversed}' niet wijzigen!", self::VALIDATION_STATE_INVALID);
                    continue;
                }

                $item->fillWithPostData();
                $item->action = "U";
                if ($fields['random']) $item->password = User::generatePassword();
                $item->setPassword = $item->password;

                $repo->set($item);

                $this->setToast("Het wachtwoord van '{$item->linked->employee->formatted->fullNameReversed}' wordt gewijzigd naar '{$item->password}'.");
            }

            $this->setCloseModal('changePassword');
            $this->setResetForm();
        }
    }

    protected function postOverviewPrint($view, $id = null)
    {
        $configRepo = new StudentConfig;
        $repo = new Student;
        $folder = LOCATION_FILES . "/student/";

        $id = explode("_", $id);
        $_fields = [
            "random" => ["what" => Input::INPUT_TYPE_STRING],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            FileSystem::CreateFolder($folder);
            $pdf = new PDF("prints", $folder . date("YmdHis") . ".pdf", "L");

            foreach ($id as $_id) {
                $config = $configRepo->getById($_id);
            }

            $pdf->save();
        }
    }
}
