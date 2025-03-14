<?php

namespace Controllers\API;

use Router\Helpers;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
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
    // Get Functions
    protected function getEmployee($view, $id = null)
    {
        $repo = new Employee;
        $filters = [
            'active' => true
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "type" => "checkbox",
                        "data" => null,
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "20px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }

    protected function getClassgroup($view, $id = null)
    {
        $repo = new ClassGroup;
        $schoolId = Helpers::url()->getParam("schoolId");
        $institutes = (new Institute)->getBySchoolId($schoolId);

        $subgroups = [];
        foreach ($institutes as $institute) {
            $sgs = $repo->getBySchoolInstituteId($institute->id);

            foreach ($sgs as $sg) $subgroups[] = $sg;
        }

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($subgroups, "code");
            General::filter($items, ['schoolyear' => INFORMAT_CURRENT_SCHOOLYEAR, 'type' => "C"]);
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
        $informatClassgroupId = Helpers::url()->getParam("informatSubgroupId");
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
