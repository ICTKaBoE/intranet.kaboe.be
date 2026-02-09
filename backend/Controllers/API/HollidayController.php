<?php

namespace Controllers\API;

use Helpers\Form;
use Helpers\Table;
use Helpers\Filter;
use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Holliday;
use Database\Object\Holliday as ObjectHolliday;

class HollidayController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "holliday";

    // Get functions
    protected function getGeneral($view, $id = null)
    {
        $repo = new Holliday;
        $items = $repo->getAfterToday();

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format(checkbox: false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $this->appendToJson("rows", $items);
        }
    }

    protected function getSchool($view, $id = null)
    {
        $repo = new Holliday;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find(['schoolId']);
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            ];

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getList($view, $id = null)
    {
        $repo = new Holliday;
        $items = $repo->get($id);

        if (Strings::equal($view, self::VIEW_CALENDAR)) {
            foreach ($items as $item) {
                $this->appendToJson(data: [
                    "id" => $item->id,
                    "start" => $item->start,
                    "end" => ($item->fullDay ? Clock::at($item->end)->plusDays(1)->format("Y-m-d") : $item->end),
                    "title" => ($item->linked->school ? $item->linked->school->name . ": " : "") . $item->name,
                    "allDay" => $item->fullDay
                ]);
            }
        }
    }

    // post functions
    protected function postSchool($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, 'type' => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "start" => ["mandatory" => true],
            "end" => ["mandatory" => true],
            "fullDay" => ["convert" => "bool"]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Holliday;

            $item = $repo->getById($id) ?? new ObjectHolliday;
            $item->fillWithPostData();

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De verlofdag is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }
}
