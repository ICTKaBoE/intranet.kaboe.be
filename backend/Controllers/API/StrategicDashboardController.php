<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Object\StrategicDashboard\Item as StrategicDashboardItem;
use Database\Object\StrategicDashboard\ItemValue as StrategicDashboardItemValue;
use Database\Repository\StrategicDashboard\Item;
use Database\Repository\StrategicDashboard\ItemType;
use Database\Repository\StrategicDashboard\ItemValue;
use Helpers\Filter;
use Helpers\Form;
use Helpers\General;
use Helpers\Table;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\Input;
use Security\User;

class StrategicDashboardController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "strategicDashboard";

    // Get Functions
    protected function getDashboard($view, $id = null)
    {
        $repo = new Item;
        $valueRepo = new ItemValue;
        $filters = Filter::Find(['schoolId']);

        if (!$id && Strings::equal($view, self::VIEW_LIST)) {
            $items = $repo->get(filters: $filters);
            $items = array_values(Arrays::filter($items, fn($i) => User::getLoggedInUser()->system || Arrays::contains(explode(";", $i->canEditUserId), User::getLoggedInUser()->id)));
            Arrays::each($items, fn($i) => $i->formatted->html = General::processTemplate([$valueRepo->getLastValueByItemId($i->id)], $i->formatted->html));

            $this->appendToJson('raw', General::processTemplate($items));
        } else if ($id && Strings::equal($view, self::VIEW_CHART)) {
            $filters = Filter::Find(['type']);
            $lastValue = $valueRepo->getLastValueByItemId($id);
            $lastValue = json_decode($lastValue->value, true);

            $series = $labels = [];

            if ($filters['type'][0] == 'bar') {
                foreach ($lastValue as $name => $value) $series[] = ['x' => $name, 'y' => $value];
                $this->appendToJson(['series', 0, 'data'], $series);
            } else if ($filters['type'][0] == 'pie') {
                foreach ($lastValue as $name => $value) {
                    $labels[] = $name;
                    $series[] = $value;
                }

                $this->appendToJson(['options', 'labels'], $labels);
                $this->appendToJson('series', $series);
            }
        }
    }


    protected function getItems($view, $id = null)
    {
        $repo = new Item;
        $filters = Filter::Find(['schoolId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id, filters: $filters);
            $items = array_values(Arrays::filter($items, fn($i) => User::getLoggedInUser()->system || Arrays::contains(explode(";", $i->canEditUserId), User::getLoggedInUser()->id)));
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getItemsType($view, $id = null)
    {
        $repo = new ItemType;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    // Post functions
    protected function postInsert($view, $id = null)
    {
        $_fields = [
            "schoolId" => ['type' => Input::INPUT_TYPE_INT],
            "itemId" => ['type' => Input::INPUT_TYPE_INT, "mandatory" => true],
            "value"
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $newValue = new StrategicDashboardItemValue;
            $item = (new Item)->getById($fields['itemId']);
            $newValue->itemId = $fields['itemId'];
            $newValue->fillWithPostData();
            $newValue->editedByUserId = User::getLoggedInUser()->id;

            if (Arrays::contains(["chart:pie", "chart:bar"], $item->linked->type->short)) {
                $lastValue = json_decode((new ItemValue)->getLastValueByItemId($item->id)->value, true);

                $template = json_decode($item->valueTemplate, true);
                $json = [];

                foreach ($template as $k => $v) {
                    if (Strings::startsWith($k, "LOOP:")) {
                        $k = str_replace("LOOP:", "", $k);
                        [$rep, $att] = explode("@", $k);

                        foreach ((new $rep)->get() as $i) $json[$i->$att] = General::convert(Helpers::input()->post(str_replace(" ", "_", $i->$att))->getValue() ?: ($lastValue ? $lastValue[$i->$att] : null), "int");
                    } else $json[$k] = General::convert(Helpers::input()->post(str_replace(" ", "_", $k))->getValue() ?: ($lastValue ? $lastValue[$k] : null), "int");
                }

                $newValue->value = json_encode($json);
            }

            (new ItemValue)->set($newValue);
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De laatste waarden zijn opgeslagen!");
            $this->setResetForm();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postItems($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ['type' => Input::INPUT_TYPE_INT, "default" => 0],
            "typeId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "minimum" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "target" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "width" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Item;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? (new StrategicDashboardItem);
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het item is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }
}
