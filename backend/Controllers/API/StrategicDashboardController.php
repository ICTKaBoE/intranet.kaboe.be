<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Object\StrategicDashboard\Category as StrategicDashboardCategory;
use Database\Object\StrategicDashboard\Item as StrategicDashboardItem;
use Database\Object\StrategicDashboard\ItemValue as StrategicDashboardItemValue;
use Database\Repository\StrategicDashboard\Category;
use Database\Repository\StrategicDashboard\Item;
use Database\Repository\StrategicDashboard\ItemType;
use Database\Repository\StrategicDashboard\ItemValue;
use Helpers\CString;
use Helpers\Filter;
use Helpers\Form;
use Helpers\General;
use Helpers\Table;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\FileSystem;
use Security\Input;
use Security\User;

class StrategicDashboardController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "strategicDashboard";

    const TEMPLATE_CARD = "
    <div class='col-12 col-lg-@width@'>
        <a href='./dashboard/@id@' class='card mb-3'>
            <div class='card-header' data-bs-position='top' data-bs-trigger='hover' data-bs-toggle='popover' data-bs-container='body' title='Extra informatie' data-bs-content='@info@'>
                @linked.school.formatted.badge.color@
                <h1 class='card-title ms-2'>@name@</h1>
            </div>
            
            <div class='card-body text-center text-white bg-@bordercolor@'>@formatted.html@</div>
            
            <div class='card-footer p-1'><span class='me-3'><i class='icon ti ti-math-min me-2'></i>@formatted.minimum@</span> <span><i class='icon ti ti-math-max me-2'></i>@formatted.target@</span></div>
        </a>
    </div>";

    // Get Functions
    protected function getDashboard($view, $id = null)
    {
        $repo = new Item;
        $valueRepo = new ItemValue;
        $filters = Filter::Find(['schoolId']);

        if (!$id && Strings::equal($view, self::VIEW_LIST)) {
            $categories = (new Category)->get();
            foreach ($categories as $category) {
                $items = $repo->getByCategoryId($category->id);
                $items = array_values(Arrays::filter($items, fn($i) => User::getLoggedInUser()->system || Arrays::contains(explode(";", $i->canEditUserId), User::getLoggedInUser()->id)));
                Arrays::each($items, fn($i) => $i->lastValue = $valueRepo->getLastValueByItemId($i->id));
                Arrays::each($items, fn($i) => $i->formatted->html = General::processTemplate([$i->lastValue], $i->formatted->html));
                Arrays::each($items, function ($i) {
                    if ($i->minimum == $i->target) $i->bordercolor = $i->lastValue ? ($i->lastValue->value < $i->minimum ? "danger" : ($i->lastValue->value > $i->target ? "success" : "warning")) : "secondary";
                    else if ($i->minimum > $i->target) $i->bordercolor = $i->lastValue ? ($i->lastValue->value > $i->minimum ? "danger" : ($i->lastValue->value <= $i->target ? "success" : "warning")) : "secondary";
                    else if ($i->minimum < $i->target) $i->bordercolor = $i->lastValue ? ($i->lastValue->value < $i->minimum ? "danger" : ($i->lastValue->value >= $i->target ? "success" : "warning")) : "secondary";
                });

                $category->items = General::processTemplate($items, self::TEMPLATE_CARD);
            }

            $this->appendToJson('raw', General::processTemplate($categories));
        } else if ($id && Strings::equal($view, self::VIEW_LIST)) {
            $item = $repo->getById($id);
            $item->currentValue = $valueRepo->getLastValueByItemId($id)->value;
            $item->formatted->currentValue = is_int(General::convert($item->currentValue, Input::INPUT_TYPE_INT)) ? CString::formatNumber($item->currentValue, 2) : $item->currentValue;
            $this->appendToJson('raw', General::processTemplate([$item]));
        } else if ($id && Strings::equal($view, self::VIEW_CHART)) {
            $filters = Filter::Find(['type', 'vw']);

            if (empty(Arrays::getNestedValue($filters, ['vw', 0]))) {
                $lastValue = $valueRepo->getLastValueByItemId($id);
                $lastValue = json_decode($lastValue->value, true);

                $series = $labels = [];

                if (Strings::equal(Arrays::getNestedValue($filters, ['type', 0]), 'bar')) {
                    $lastValue = Arrays::filter($lastValue, fn($v) => !is_null($v) && $v !== "");
                    foreach ($lastValue as $name => $value) $series[] = ['x' => $name, 'y' => $value];
                    $this->appendToJson(['series', 0, 'data'], $series);
                } else if (Strings::equal(Arrays::getNestedValue($filters, ['type', 0]), 'pie')) {
                    foreach ($lastValue as $name => $value) {
                        $labels[] = $name;
                        $series[] = $value;
                    }

                    $this->appendToJson(['options', 'labels'], $labels);
                    $this->appendToJson('series', $series);
                }
            } else {
                $history = $valueRepo->getByItemId($id);

                if (count($history) == 1) {
                    $this->appendToJson(['series', 0, 'data'], []);
                    return;
                }
                $history = Arrays::map($history, fn($h) => ['x' => $h->datetime, 'y' => $h->value]);
                $today = $valueRepo->getLastValueByItemId($id);
                Arrays::concat($history, [['x' => Clock::nowAsString("Y-m-d H:i:s"), 'y' => $today->value]]);
                $this->appendToJson(['series', 0, 'data'], $history);

                $item = $repo->getById($id);
                $treshholds = [
                    [
                        "y" => $item->minimum,
                        "borderColor" => "red",
                        "label" => [
                            "borderColor" => "red",
                            "sytle" => [
                                "color" => "white",
                                "background" => "red"
                            ],
                            "text" => "Minimum waarde: {$item->minimum}"
                        ]
                    ],
                    [
                        "y" => $item->target,
                        "borderColor" => "green",
                        "label" => [
                            "borderColor" => "green",
                            "sytle" => [
                                "color" => "white",
                                "background" => "green"
                            ],
                            "text" => "Target waarde: {$item->target}"
                        ]
                    ]
                ];
                $this->appendToJson(['options', 'annotations', 'yaxis'], $treshholds);
            }
        }
    }

    protected function getCategories($view, $id = null)
    {
        $repo = new Category;
        $filters = Filter::Find();

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get();
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) $this->appendToJson('items', Arrays::map($repo->get(), fn($i) => $i->toArray(true)));
        else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
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
            $newValue->fillWithPostData();
            $newValue->editedByUserId = User::getLoggedInUser()->id;

            $item = (new Item)->getById($newValue->itemId);
            if (Arrays::contains(["chart:pie", "chart:bar"], $item->linked->type->short)) {
                $lastValue = json_decode((new ItemValue)->getLastValueByItemId($item->id)->value, true);
                $template = json_decode(str_replace(PHP_EOL, "", $item->valueTemplate), true);
                $json = [];

                foreach ($template as $k => $v) {
                    if (Strings::startsWith($k, "LOOP:")) {
                        [$rep, $att] = explode("@", str_replace("LOOP:", "", $k));

                        foreach ((new $rep)->get() as $i) $json[$i->$att] = General::convert(Helpers::input()->post(str_replace([" ", "."], "_", $i->$att))->getValue() ?: ($lastValue ? $lastValue[$i->$att] : null), "int");
                    } else $json[$k] = General::convert(Helpers::input()->post(str_replace([" ", "."], "_", $k))->getValue() ?: ($lastValue ? $lastValue[$k] : null), "int");
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

    protected function postCategories($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "name" => ["mandatory" => true],
            "order" => ["mandatory" => true],
            "image" => ["type" => "file"],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Category;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? (new StrategicDashboardCategory);
                $item->fillWithPostData();

                $nId = $repo->set($item);
                if (is_null($id)) $item = $repo->getById($nId);

                if ($fields['image']) {
                    $location = LOCATION_FILES . "/strategicDashboard";
                    FileSystem::CreateFolder($location);

                    foreach ($fields['image'] as $index => $attachment) {
                        $attachment->move("{$location}/{$item->guid}." . $attachment->getExtension());
                        $item->image = true;
                    }

                    $repo->set($item);
                }
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het item is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Delete functions
    protected function deleteItems($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Item;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het item '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }
}
