<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\General\Schoolyear;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Informat\Student;
use Database\Repository\School\Institute;
use Helpers\Filter;
use Helpers\Table;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;

class StudentController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "student";

    // GET
    protected function getOverview($view, $id = null)
    {
        $repo = new Student;
        $regRepo = new Registration;
        $regClassRepo = new RegistrationClass;
        $classRepo = new ClassGroup;

        $filters = Filter::Find();
        if (Helpers::url()->hasParam('schoolId')) $filters["instituteId"] = Arrays::map((new Institute)->getBySchoolId(Helpers::url()->getParam("schoolId")), fn($i) => $i->id);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $schoolyear = Helpers::url()->hasParam('schoolyearId') ? (new Schoolyear)->getById(Helpers::url()->getParam('schoolyearId')) : (new Schoolyear)->getCurrent();
            $items = $repo->get(filters: $filters);

            foreach ($items as $index => $i) {
                $currentRegistration = $regRepo->getCurrentByInformatStudentId($i->id);

                if (!$currentRegistration) {
                    unset($items[$index]);
                    continue;
                }

                $currentRegistrationClass = $regClassRepo->getCurrentByInformatRegistrationId($currentRegistration->id);
                if (!$currentRegistrationClass) continue;

                $i->linked->registration = $currentRegistrationClass;
                $i->linked->class = $classRepo->getById($currentRegistrationClass->informatClassGroupId);
            }

            $this->appendToJson("rows", array_values($items));
        }
    }
}
