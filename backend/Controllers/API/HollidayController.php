<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\Holliday as ObjectHolliday;
use Database\Repository\Holliday;

class HollidayController extends ApiController
{
    // Get functions
    protected function getGeneral($view, $id = null)
    {
        $repo = new Holliday;
        $items = $repo->get($id);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            ];

            $this->appendToJson("checkbox", false);
            $this->appendToJson("defaultOrder", [[1, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Start",
                        "data" => "formatted.start",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Einde",
                        "data" => "formatted.end",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date",
                        "width" => "200px"
                    ],
                ]
            );

            General::filter($items, $filters);
            $this->appendToJson("rows", array_values($items));
        }
    }

    protected function getSchool($view, $id = null)
    {
        $repo = new Holliday;
        $items = $repo->get($id);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            ];

            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[3, "asc"]]);
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
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Start",
                        "data" => "formatted.start",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Einde",
                        "data" => "formatted.end",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "type" => "date",
                        "width" => "200px"
                    ],
                ]
            );

            General::filter($items, $filters);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::first($repo->get($id)));
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
                    "end" => $item->end,
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

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $name = Helpers::input()->post('name')->getValue();
        $start = Helpers::input()->post('start')->getValue();
        $end = Helpers::input()->post('end')->getValue();
        $fullDay = Helpers::input()->post("fullDay")->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($start) || Input::empty($start)) $this->setValidation("start", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($end) || Input::empty($end)) $this->setValidation("end", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Holliday;

            $item = $id ? Arrays::first($repo->get($id)) : new ObjectHolliday;
            $item->schoolId = $schoolId;
            $item->name = $name;
            $item->start = $start;
            $item->end = $end;
            $item->fullDay = General::convert($fullDay, "bool");

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De verlofdag is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }
}
