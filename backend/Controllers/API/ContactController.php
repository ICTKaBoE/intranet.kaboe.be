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
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\School;
use Database\Repository\Sync\AD\Staff;
use Database\Repository\SchoolInstitute;
use Database\Repository\Informat\Student;
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

        if (Strings::equal($view, self::VIEW_LIST)) {
            $items = $repo->get();
            $search = Helpers::url()->getParam("search");
            $page = Helpers::url()->getParam("page", 0);
            $limit = Helpers::url()->getParam("limit");

            if ($search) {
                $items = Arrays::filter($items, fn($s) => Strings::containsIgnoreCase(json_encode($s->toSearchArray()), $search));
            }

            if ($page || $limit) {
                $start = $page * $limit;
                $items = array_slice($items, $start);

                $this->appendToJson("next", count($items) > $limit);
                $items = array_slice($items, 0, $limit);
            }

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
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    protected function getStudent($view, $id = null)
    {
        $repo = new Student;

        if (Strings::equal($view, self::VIEW_LIST)) {
            $items = $repo->get();

            $search = Helpers::url()->getParam("search");
            $page = Helpers::url()->getParam("page", 0);
            $limit = Helpers::url()->getParam("limit");

            $registrationRepo = new Registration;
            $registrationClassRepo = new RegistrationClass;
            $classgroupRepo = new ClassGroup;
            $instituteRepo = new SchoolInstitute;
            $schoolRepo = new School;

            if ($search) {
                $items = Arrays::filter($items, fn($s) => Strings::containsIgnoreCase(json_encode($s->toSearchArray()), $search));
            }

            if ($page || $limit) {
                $start = $page * $limit;
                $items = array_slice($items, $start);

                $this->appendToJson("next", count($items) > $limit);
                $items = array_slice($items, 0, $limit);
            }

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
                $currentRegistrationClass = Arrays::firstOrNull($currentRegistrationClass);

                if (!$currentRegistrationClass) continue;
                $i->linked->registration = $currentRegistrationClass;
                $i->linked->class = Arrays::firstOrNull($classgroupRepo->get($currentRegistrationClass->informatClassGroupId));
            }

            $items = Arrays::map(array_values($items), fn($i) => $i->toArray(true));
            $this->appendToJson('_raw', "base64");
            $this->appendToJson('raw', base64_encode(General::processTemplate($items)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::first($repo->get($id)));
    }

    // Post functions

    // Delete functions
}
