<?php

namespace Controllers\API;

use Helpers\HTML;
use Router\Helpers;
use Helpers\General;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\EmployeeAddress;
use Database\Repository\Informat\EmployeeEmail;
use Database\Repository\Informat\EmployeeNumber;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\School;
use Database\Repository\Sync\AD\Staff;
use Database\Repository\SchoolInstitute;
use Database\Repository\Informat\Student;
use Database\Repository\Informat\StudentAddress;
use Database\Repository\Informat\StudentBank;
use Database\Repository\Informat\StudentEmail;
use Database\Repository\Informat\StudentNumber;
use Database\Repository\Informat\StudentRelation;
use Database\Repository\Informat\Teacher;
use Database\Repository\Informat\Subgroup;
use Database\Repository\Informat\SubgroupStudent;
use Database\Repository\Informat\TeacherFreefield;

class ContactController extends ApiController
{
    // Get functions
    protected function getStaff($view, $id = null)
    {
        $repo = new Employee;
        $ownfieldRepo = new EmployeeOwnfield;
        $sRepo = new School;
        $addressRepo = new EmployeeAddress;
        $emailRepo = new EmployeeEmail;
        $numberRepo = new EmployeeNumber;

        if (Strings::equal($view, self::VIEW_LIST)) {
            if (!$id) {
                $items = $repo->get();
                $search = Helpers::url()->getParam("search");

                if ($search) {
                    $items = Arrays::filter($items, fn($s) => Strings::containsIgnoreCase(json_encode($s->toSearchArray()), $search));
                }

                $this->appendToJson('next', General::hasNextPage($items));
                General::page($items);

                Arrays::each($items, function ($i) use ($ownfieldRepo, $sRepo) {
                    $functionsPerSchool = [];
                    $ownfields = $ownfieldRepo->getByInformatEmployeeIdAndSection($i->id, 2);
                    $ownfields = Arrays::filter($ownfields, fn($of) => Strings::contains($of->name, " - Functie "));
                    $ownfields = Arrays::orderBy($ownfields, "name");

                    foreach ($ownfields as $of) {
                        $school = Arrays::first(explode(" - ", $of->name));
                        $school = $sRepo->getByName($school);

                        $functionsPerSchool[$school->name]["school"] = $school;
                        $functionsPerSchool[$school->name]["functions"][] = Arrays::first(explode(" (", $of->value));
                    }

                    $i->formatted->functionsPerSchool = "";
                    foreach ($functionsPerSchool as $fps) {
                        $i->formatted->functionsPerSchool .= "<p>";
                        $i->formatted->functionsPerSchool .= $fps['school']->formatted->badge->name . "<br />";

                        foreach ($fps['functions'] as $f) {
                            $i->formatted->functionsPerSchool .= $f . "<br />";
                        }

                        $i->formatted->functionsPerSchool .= "</p>";
                    }
                });

                $items = Arrays::map(array_values($items), fn($i) => $i->toArray(true));

                $this->appendToJson('_raw', "base64");
                $this->appendToJson('raw', base64_encode(General::processTemplate($items)));
            } else {
                $item = Arrays::firstOrNull($repo->get($id));
                if (!$item) return;

                $ownfields = $ownfieldRepo->getByInformatEmployeeIdAndSection($item->id, 2);
                $ownfields = Arrays::filter($ownfields, fn($ff) => Strings::contains($ff->name, " - Functie "));

                $schools = Arrays::map($ownfields, fn($s) => $s->name);
                $schools = Arrays::map($schools, fn($s) => Arrays::first(explode(" - ", $s)));
                $schools = array_unique(array_values($schools));
                $schools = Arrays::map($schools, fn($s) => $sRepo->getByName($s));
                $schools = Arrays::map($schools, fn($s) => $s->formatted->badge->name);
                $schools = implode("<br />", $schools);

                $functions = Arrays::map($ownfields, fn($s) => $s->value);
                $functions = array_unique(array_values($functions));
                $functions = Arrays::map($functions, fn($f) => Arrays::first(explode(" (", $f)));
                $functions = implode("<br />", $functions);

                $addresses = $addressRepo->getByInformatEmployeeId($item->id);
                $addresses = Arrays::map($addresses, fn($a) => $a->formatted->address);
                $addresses = implode("<br />", $addresses);

                $emails = $emailRepo->getByInformatEmployeeId($item->id);
                $emails = Arrays::map($emails, fn($e) => "{$e->type}:\t" . $e->formatted->link);
                $emails = implode("<br />", $emails);

                $numbers = $numberRepo->getByInformatEmployeeId($item->id);
                $numbers = Arrays::map($numbers, fn($n) => "{$n->type} - {$n->category}\t" . $n->formatted->link);
                $numbers = implode("<br />", $numbers);


                $items = [
                    [
                        "title" => "Naam",
                        "content" => $item->name
                    ],
                    [
                        "title" => "Voornaam",
                        "content" => $item->firstName
                    ],
                    [
                        "title" => "Stamnummer",
                        "content" => $item->basenumber
                    ],
                    [
                        "title" => "School",
                        "content" => $schools
                    ],
                    [
                        "title" => "Functie",
                        "content" => $functions
                    ],
                    [
                        "title" => "Telefoon",
                        "content" => $numbers
                    ],
                    [
                        "title" => "E-mail",
                        "content" => $emails
                    ],

                    [
                        "title" => "Adres",
                        "content" => $addresses
                    ],
                    [
                        "informatGuid" => $item->informatGuid,
                        "fullNameReversed" => $item->formatted->fullNameReversed
                    ]
                ];

                $this->appendToJson('raw', General::processTemplate($items, searchPrePost: "&"));
            }
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    protected function getStudent($view, $id = null)
    {
        $repo = new Student;

        $addressRepo = new StudentAddress;
        $relationRepo = new StudentRelation;
        $numberRepo = new StudentNumber;
        $emailRepo = new StudentEmail;
        $bankRepo = new StudentBank;

        $registrationRepo = new Registration;
        $registrationClassRepo = new RegistrationClass;
        $classgroupRepo = new ClassGroup;
        $instituteRepo = new SchoolInstitute;
        $schoolRepo = new School;

        if (Strings::equal($view, self::VIEW_LIST)) {
            if (!$id) {
                $items = $repo->get();

                $search = Helpers::url()->getParam("search");

                if ($search) {
                    $items = Arrays::filter($items, fn($s) => Strings::containsIgnoreCase(json_encode($s->toSearchArray()), $search));
                }

                $this->appendToJson("next", General::hasNextPage($items));
                General::page($items);

                foreach ($items as $index => $i) {
                    $currentRegistration = $registrationRepo->getByInformatStudentId($i->id);
                    $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => Clock::now()->isAfterOrEqualTo(Clock::at($cr->start)) && (is_null($cr->end) || Clock::now()->isBeforeOrEqualTo(Clock::at($cr->end))));
                    $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => $cr->status == 0);
                    $currentRegistration = Arrays::firstOrNull($currentRegistration);

                    if (!$currentRegistration) {
                        unset($items[$index]);
                        continue;
                    }

                    $institute = Arrays::firstOrNull($instituteRepo->get($currentRegistration->schoolInstituteId));
                    $school = Arrays::firstOrNull($schoolRepo->get($institute->schoolId));
                    $i->linked->school = $school;

                    $currentRegistrationClass = $registrationClassRepo->getByInformatRegistrationId($currentRegistration->id);
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => Clock::now()->isAfterOrEqualTo(Clock::at($crc->start)) && (is_null($crc->end) || Clock::now()->isBeforeOrEqualTo(Clock::at($crc->end))));
                    $currentRegistrationClass = array_reverse(Arrays::orderBy($currentRegistrationClass, 'start'));
                    $currentRegistrationClass = Arrays::firstOrNull($currentRegistrationClass);

                    if (!$currentRegistrationClass) continue;
                    $i->linked->registration = $currentRegistrationClass;
                    $i->linked->class = Arrays::firstOrNull($classgroupRepo->get($currentRegistrationClass->informatClassGroupId));
                }

                $items = Arrays::map(array_values($items), fn($i) => $i->toArray(true));
                $this->appendToJson('_raw', "base64");
                $this->appendToJson('raw', base64_encode(General::processTemplate($items)));
            } else {
                $item = Arrays::firstOrNull($repo->get($id));
                if (!$item) return;

                $addresses = $addressRepo->getByInformatStudentId($item->id);
                $addresses = Arrays::map($addresses, fn($a) => $a->formatted->full);
                $addresses = implode("<br />", $addresses);

                $relations = $relationRepo->getByInformatStudentId($item->id);
                $relations = Arrays::map($relations, fn($r) => $r->formatted->typeWithFullNameReversed);
                $relations = implode("<br />", $relations);

                $numbers = $numberRepo->getByInformatStudentId($item->id);
                $numbers = Arrays::map($numbers, fn($n) => $n->formatted->typeWithLink);
                $numbers = implode("<br />", $numbers);

                $emails = $emailRepo->getByInformatStudentId($item->id);
                $emails = Arrays::map($emails, fn($e) => $e->formatted->typeWithLink);
                $emails = implode("<br />", $emails);

                $banks = $bankRepo->getByInformatStudentId($item->id);
                $banks = Arrays::map($banks, fn($b) => $b->formatted->details);
                $banks = implode("<br />", $banks);

                $registrations = $registrationRepo->getByInformatStudentId($item->id);
                $registrations = Arrays::filter($registrations, fn($cr) => $cr->status == 0);
                $registrations = array_reverse(Arrays::orderBy($registrations, "start"));

                $history = "";
                foreach ($registrations as $registration) {
                    $school = $schoolRepo->get($instituteRepo->get($registration->schoolInstituteId)[0]->schoolId)[0];
                    $classRegistrations = $registrationClassRepo->getByInformatRegistrationId($registration->id);
                    $classRegistrations = array_reverse(Arrays::orderBy($classRegistrations, "start"));

                    $history .= "{$school->name} ({$registration->formatted->dates}) - Stamnummer: {$registration->basenumber}";
                    $history .= "<ul>";

                    foreach ($classRegistrations as $cr) {
                        $classgroup = Arrays::firstOrNull($classgroupRepo->get($cr->informatClassGroupId));
                        $history .= "<li>{$classgroup->code} - {$classgroup->name} ({$cr->formatted->dates})</li>";
                    }

                    $history .= "</ul>";
                }

                $items = [
                    [
                        "title" => "Naam",
                        "content" => $item->name
                    ],
                    [
                        "title" => "Voornaam",
                        "content" => $item->firstName
                    ],
                    [
                        "title" => "Geboortedatum",
                        "content" => $item->formatted->birthDate
                    ],
                    [
                        "address" => $addresses,
                        "relation" => $relations,
                        "number" => $numbers,
                        "email" => $emails,
                        "bank" => $banks,
                        "history" => $history,
                        "informatGuid" => $item->informatGuid,
                        "fullNameReversed" => $item->formatted->fullNameReversed
                    ]
                ];

                $this->appendToJson('raw', General::processTemplate($items, searchPrePost: "#"));
            }
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    // Post functions

    // Delete functions
}
