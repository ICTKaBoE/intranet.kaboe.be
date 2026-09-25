<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Object\ExamSchedule\EmployeeHour as ExamScheduleEmployeeHour;
use Database\Object\ExamSchedule\Period as ExamSchedulePeriod;
use Database\Repository\ExamSchedule\EmployeeHour;
use Database\Repository\ExamSchedule\Period;
use Database\Repository\General\Schoolyear;
use Database\Repository\Informat\Employee;
use Helpers\Filter;
use Helpers\Form;
use Helpers\Table;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\Input;

class ExamScheduleController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "examschedule";

    // Get functions
    protected function getAssign($view, $id = null)
    {
        $repo = new EmployeeHour;
        $informatEmployeeRepo = new Employee;
        $currentSchoolyearId = (new Schoolyear)->getCurrent()->id;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find(['schoolyearId']);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $informatEmployeeRepo->get();
            Arrays::each($items, fn($i) => $i->hours = $repo->getByInformatEmployeeIdAndSchoolyearId($i->id, $filters['schoolyearId'] ?: $currentSchoolyearId));
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }    
    protected function getPeriod($view, $id = null)
    {
        $repo = new Period;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find(['schoolyearId', 'schoolId']);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    // Post functions
    protected function postAssign($view, $id = null)
    {
        $repo = new EmployeeHour;

        $_fields = [
            "informatEmployeeId" => ['type' => Input::INPUT_TYPE_INT],
            "assignSchoolyearId" => ['type' => Input::INPUT_TYPE_INT],
            "workedHours" => ['type' => Input::INPUT_TYPE_INT],
            "examHours" => ["type" => Input::INPUT_TYPE_INT],
            "supervisionHours" => ["type" => Input::INPUT_TYPE_INT],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getByInformatEmployeeIdAndSchoolyearId($fields['informatEmployeeId'], $fields['assignSchoolyearId']) ?? (new ExamScheduleEmployeeHour);
            $item->fillWithPostData($fields);
            $item->schoolyearId = $fields['assignSchoolyearId'];

            $repo->set($item);

            $this->setReloadTable();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }
    
    protected function postPeriod($view, $id = null)
    {
        $repo = new Period;

        $_fields = [
            "schoolId" => ['type' => Input::INPUT_TYPE_INT],
            "schoolyearId" => ['type' => Input::INPUT_TYPE_INT],
            "name" => ['type' => Input::INPUT_TYPE_STRING],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($id) ?? (new ExamSchedulePeriod);
            $item->fillWithPostData($fields);

            $repo->set($item);

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }
}
