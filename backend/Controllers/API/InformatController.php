<?php

namespace Controllers\API;

use Router\Helpers;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\General\Schoolyear;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\ClassGroupTeacher;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Informat\Student;
use Database\Repository\Informat\StudentAddress;
use Database\Repository\Informat\StudentBank;
use Database\Repository\Informat\StudentEmail;
use Database\Repository\Informat\StudentNumber;
use Database\Repository\Informat\StudentRelation;
use Database\Repository\School\Department;
use Database\Repository\School\Institute;
use Database\Repository\School\School;
use Helpers\Filter;
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
        $classRepo = new RegistrationClass;
        $cgRepo = new ClassGroup;
        $cgtRepo = new ClassGroupTeacher;
        $studentAddressRepo = new StudentAddress;
        $studentBankRepo = new StudentBank;
        $studentEmailRepo = new StudentEmail;
        $studentNumberRepo = new StudentNumber;
        $studentRelationRepo = new StudentRelation;

        $schoolId = Helpers::url()->getParam("schoolId");
        $school = (new School)->getById($schoolId);
        $institutes = $schoolId ? (new Institute)->getBySchoolId($school->id) : (new Institute)->get();

        $schoolyear = _CURRENT_SCHOOLYEAR_;
        $nextSchoolyear = Helpers::url()->hasParam("nextSchoolyear");
        if ($nextSchoolyear) $schoolyear = General::getSchoolyear(Clock::now()->plusYears(1)->format("Y-m-d"));
        $schoolyear = (new Schoolyear)->getByName($schoolyear);

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = [];
            foreach ($institutes as $institute) {
                $students = $repo->getByInstituteId($institute->id);

                foreach ($students as $student) {
                    $registration = $nextSchoolyear ? $regRepo->getByInformatStudentId($student->id) : $regRepo->getCurrentByInformatStudentId($student->id);
                    if (!$registration) continue;

                    if ($nextSchoolyear) {
                        $registration = Arrays::firstOrNull(Arrays::filter($registration, fn($r) => Clock::at($r->start)->isAfterOrEqualTo(Clock::at($schoolyear->start)) && (is_null($r->end) || Clock::at($r->end)->isBeforeOrEqualTo(Clock::at($schoolyear->end)))));
                    }
                    if (!$registration) continue;

                    $classRegistration = $nextSchoolyear ? $classRepo->getByInformatRegistrationId($registration->id) : $classRepo->getCurrentByInformatRegistrationId($registration->id);
                    if (!$registration) continue;

                    if ($nextSchoolyear) {
                        $classRegistration = Arrays::firstOrNull(Arrays::filter($classRegistration, fn($r) => Clock::at($r->start)->isAfterOrEqualTo(Clock::at($schoolyear->start)) && (is_null($r->end) || Clock::at($r->end)->isBeforeOrEqualTo(Clock::at($schoolyear->end)))));
                    }
                    if (!$classRegistration) continue;
                    $items[] = $student;
                }
            }
            $items = Arrays::orderBy($items, "name");
            $this->appendToJson('next', General::hasNextPage($items));
            General::page($items);

            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_PS)) {
            $items = [];

            foreach ($institutes as $institute) {
                $students = $repo->getByInstituteId($institute->id);

                foreach ($students as $student) {
                    $registration = $nextSchoolyear ? $regRepo->getByInformatStudentId($student->id) : $regRepo->getCurrentByInformatStudentId($student->id);
                    if (!$registration) continue;

                    if ($nextSchoolyear) $registration = Arrays::firstOrNull(Arrays::filter($registration, fn($r) => Clock::at($r->start)->isAfterOrEqualTo(Clock::at($schoolyear->start)) && (is_null($r->end) || Clock::at($r->end)->isBeforeOrEqualTo(Clock::at($schoolyear->end)))));
                    if (!$registration) continue;

                    $classRegistration = $nextSchoolyear ? $classRepo->getByInformatRegistrationId($registration->id) : $classRepo->getCurrentByInformatRegistrationId($registration->id);
                    if (!$registration) continue;

                    if ($nextSchoolyear) $classRegistration = Arrays::firstOrNull(Arrays::filter($classRegistration, fn($r) => Clock::at($r->start)->isAfterOrEqualTo(Clock::at($schoolyear->start)) && (is_null($r->end) || Clock::at($r->end)->isBeforeOrEqualTo(Clock::at($schoolyear->end)))));
                    if (!$classRegistration) continue;

                    $student->registration = $registration;
                    $student->classRegistration = $classRegistration;
                    $student->class = $cgRepo->getById($classRegistration->informatClassGroupId);
                    $student->class->teachers = Arrays::map($cgtRepo->getByInformatClassgroupId($student->class->id), fn($cgt) => $cgt->linked->informatEmployee);
                    $student->institute = $student->class->linked->schoolInstitute;
                    $student->addresses = $studentAddressRepo->getByInformatStudentId($student->id);
                    $student->banks = $studentBankRepo->getByInformatStudentId($student->id);
                    $student->emails = $studentEmailRepo->getByInformatStudentId($student->id);
                    $student->numbers = $studentNumberRepo->getByInformatStudentId($student->id);
                    $student->relations = $studentRelationRepo->getByInformatStudentId($student->id);

                    unset($student->id, $student->instituteId);
                    unset($student->mapped, $student->linked, $student->formatted);
                    unset($student->registration->id, $student->registration->informatStudentId, $student->registration->schoolInstituteId, $student->registration->status, $student->registration->current, $student->registration->mapped, $student->registration->linked, $student->registration->formatted);
                    unset($student->classRegistration->id, $student->classRegistration->informatRegistrationId, $student->classRegistration->informatClassgroupId, $student->classRegistration->current, $student->classRegistration->mapped, $student->classRegistration->linked, $student->classRegistration->formatted);
                    unset($student->class->id, $student->class->schoolInstituteId, $student->class->mapped, $student->class->linked, $student->class->formatted);
                    unset($student->institute->id, $student->institute->schoolId, $student->institute->deleted, $student->institute->mapped, $student->institute->linked, $student->institute->formatted);
                    foreach ($student->addresses as $a) unset($a->id, $a->informatStudentId, $a->mapped, $a->linked, $a->formatted);
                    foreach ($student->banks as $b) unset($b->id, $b->informatStudentId, $b->mapped, $b->linked, $b->formatted);
                    foreach ($student->emails as $e) unset($e->id, $e->informatStudentId, $e->mapped, $e->linked, $e->formatted);
                    foreach ($student->numbers as $n) unset($n->id, $n->informatStudentId, $n->mapped, $n->linked, $n->formatted);
                    foreach ($student->relations as $r) unset($r->id, $r->informatStudentId, $r->mapped, $r->linked, $r->formatted);
                    foreach ($student->class->teachers as $t) unset($t->id, $t->active, $t->instituteId, $t->mapped, $t->linked, $t->formatted);
                    $items[] = $student;
                }
            }

            $this->appendToJson("students", $items);
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
                $student->linked->class = $class;
                $students[] = $student;
            }

            $students = Arrays::orderBy($students, "name");
            $items = array_merge($items, $students);
        }

        $this->appendToJson('optgroups', $optgroups);
        $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
    }

    protected function getStudentPerClassByDepartment($view, $id = null)
    {
        $repo = new Student;
        $filters = Filter::Find(['schoolId', 'departmentId']);

        $classRepo = new ClassGroup;

        $optgroups = [];
        $items = [];

        $department = (new Department)->getById($filters['departmentId'][0]);
        foreach (explode(";", $department->informatClassId) as $classId) {
            $class = $classRepo->getById($classId);
            $optgroups[] = $class;
        }
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
                $student->linked->class = $class;
                $student->formatted->name = "{$class->code} - {$student->formatted->fullNameReversed}";
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
