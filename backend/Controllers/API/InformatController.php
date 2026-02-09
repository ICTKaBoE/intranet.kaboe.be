<?php

namespace Controllers\API;

use Router\Helpers;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\General\Schoolyear;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Informat\Student;
use Database\Repository\Informat\StudentAddress;
use Database\Repository\Informat\StudentBank;
use Database\Repository\Informat\StudentEmail;
use Database\Repository\Informat\StudentNumber;
use Database\Repository\Informat\StudentRelation;
use Database\Repository\School\Institute;
use Ouzo\Utilities\Clock;

class InformatController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "informat";

    // Get Functions
    protected function getEmployee($view, $id = null)
    {
        $repo = new Employee;
        $filters = [
            'active' => true
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[1, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getClassgroup($view, $id = null)
    {
        $repo = new ClassGroup;
        $schoolId = Helpers::url()->getParam("fast_schoolId", Helpers::url()->getParam("schoolId"));
        $institutes = (new Institute)->getBySchoolId($schoolId);
        $currentSchoolyear = (new Schoolyear)->getCurrent()->name;

        $subgroups = [];
        foreach ($institutes as $institute) {
            $filters = [
                'schoolInstituteId' => $institute->id,
                'schoolyear' => $currentSchoolyear,
                'type' => "C"
            ];
            $sgs = $repo->get(filters: $filters);

            foreach ($sgs as $sg) $subgroups[] = $sg;
        }

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($subgroups, "code");
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getStudent($view, $id = null)
    {
        $repo = new Student;
        $regRepo = new Registration;
        $schoolId = Helpers::url()->getParam("schoolId");
        $institutes = $schoolId ? (new Institute)->getBySchoolId($schoolId) : (new Institute)->get();

        $items = [];
        foreach ($institutes as $institute) {
            $regs = $regRepo->getBySchoolInstituteId($institute->id);

            foreach ($regs as $reg) {
                if (!is_null($reg->end)) continue;
                if (Clock::at($reg->start)->isAfter(Clock::now())) continue;

                $student = $repo->get($reg->informatStudentId)[0];
                if (!$student) continue;

                $items[] = $student;
            }
        }

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($items, "name");
            $this->appendToJson('next', General::hasNextPage($items));
            General::page($items);

            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getStudentByClass($view, $id = null)
    {
        $repo = new Student;
        $informatClassgroupId = Helpers::url()->getParam("informatSubgroupId", Helpers::url()->getParam("classgroupId"));
        if (!$informatClassgroupId) return;

        $registrationClasses = (new RegistrationClass)->getByInformatClassgroupId($informatClassgroupId);

        $students = [];
        $registrationRepo = new Registration;
        foreach ($registrationClasses as $rc) {
            if (!$rc->current) continue;

            $registration = Arrays::firstOrNull($registrationRepo->get($rc->informatRegistrationId));
            if (!$registration) continue;

            $students[] = Arrays::firstOrNull($repo->get($registration->informatStudentId));
        }

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($students, "name");
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getStudentPerClass($view, $id = null)
    {
        $repo = new Student;
        $schoolId = Helpers::url()->getParam("schoolId");

        $insituteRepo = new Institute;
        $classRepo = new ClassGroup;

        $optgroups = [];
        $items = [];

        foreach ($insituteRepo->getBySchoolId($schoolId) as $institute) $optgroups = array_merge($optgroups, $classRepo->getBySchoolInstituteIdSchoolyearAndType($institute->id, (new Schoolyear)->getCurrent()->name, 'C'));
        $optgroups = Arrays::orderBy($optgroups, "code");

        foreach ($optgroups as $class) {
            $students = [];
            $registrationClasses = (new RegistrationClass)->getByInformatClassgroupId($class->id);

            $registrationRepo = new Registration;
            foreach ($registrationClasses as $rc) {
                if (!$rc->current) continue;

                $registration = $registrationRepo->getById($rc->informatRegistrationId);
                if (!$registration) continue;

                $student = $repo->getById($registration->informatStudentId);
                $student->optgroup = $class->id;
                $student->class = $class;
                $students[] = $student;
            }

            $students = Arrays::orderBy($students, "name");
            $items = array_merge($items, $students);
        }

        $this->appendToJson('optgroups', $optgroups);
        $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
    }

    protected function getStudentAddress($view, $id = null)
    {
        $repo = new StudentAddress;
        $informatStudentId = Helpers::url()->getParam("informatStudentId");

        $items = $repo->getByInformatStudentId($informatStudentId);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getStudentRelation($view, $id = null)
    {
        $repo = new StudentRelation;
        $informatStudentId = Helpers::url()->getParam("informatStudentId");

        $items = $repo->getByInformatStudentId($informatStudentId);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($items, "type");
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getStudentEmail($view, $id = null)
    {
        $repo = new StudentEmail;
        $informatStudentId = Helpers::url()->getParam("informatStudentId");

        $items = $repo->getByInformatStudentId($informatStudentId);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($items, "type");
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getStudentNumber($view, $id = null)
    {
        $repo = new StudentNumber;
        $informatStudentId = Helpers::url()->getParam("informatStudentId");

        $items = $repo->getByInformatStudentId($informatStudentId);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($items, "type");
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getStudentBank($view, $id = null)
    {
        $repo = new StudentBank;
        $informatStudentId = Helpers::url()->getParam("informatStudentId");

        $items = $repo->getByInformatStudentId($informatStudentId);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($items, "type");
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }
}
