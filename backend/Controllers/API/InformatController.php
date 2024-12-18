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
use Database\Repository\Informat\Subgroup;
use Database\Repository\Informat\SubgroupStudent;
use Database\Repository\Informat\Teacher;
use Database\Repository\SchoolInstitute;
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
        $institutes = (new SchoolInstitute)->getBySchoolId($schoolId);

        $subgroups = [];
        foreach ($institutes as $institute) {
            $sgs = $repo->getBySchoolInstituteId($institute->id);

            foreach ($sgs as $sg) $subgroups[] = $sg;
        }

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($subgroups, "code");
            General::filter($items, ['schoolyear' => INFORMAT_CURRENT_SCHOOLYEAR]);
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
            if (!is_null($rc->end)) continue;
            if (Clock::at($rc->start)->isAfter(Clock::now())) continue;

            $registration = Arrays::firstOrNull($registrationRepo->get($rc->informatRegistrationId));
            if (!$registration) continue;

            $students[] = Arrays::firstOrNull($repo->get($registration->informatStudentId));
        }

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = Arrays::orderBy($students, "name");
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray(true)));
        }
    }
}
