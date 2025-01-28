<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Sync;
use Controllers\ApiController;
use Database\Repository\Navigation;

class SyncController extends ApiController
{

    protected function getEmployee($view, $id = null)
    {
        $this->getList($view, $id, "E");
    }

    protected function getStudent($view, $id = null)
    {
        $this->getList($view, $id, "S");
    }

    protected function getList($view, $id = null, $type = null)
    {
        $repo = new Sync;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                "type" => $type
            ];

            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[3, "asc"], [4, "asc"]]);
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
                        "title" => "Komende actie",
                        "data" => "formatted.badge.nextAction",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "1px"
                    ],
                    [
                        "title" => "Informat ID",
                        "data" => "employeeId",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "1px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "linked.employee.name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Voornaam",
                        "data" => "linked.employee.firstName",
                        "width" => "200px"
                    ],
                    [
                        "title" => "E-Mail",
                        "data" => "setEmail",
                    ],
                    [
                        "title" => "Wachtwoord",
                        "data" => "setPassword",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Laatste actie",
                        "data" => "formatted.badge.lastAction",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "1px"
                    ],
                    [
                        "title" => "Laatste Sync/Foutmelding",
                        "data" => "formatted.lastSyncWithError",
                        "orderable" => false,
                        "searchable" => false,
                        "widht" => "1px"
                    ]
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_PS)) {
            $items = $repo->get();
            $items = Arrays::orderBy($items, "type");
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray()));
        }
    }

    protected function getSettings($view, $id = null)
    {
        $repo = new Navigation;
        $_settings = Arrays::first($repo->get(Session::get("moduleSettingsId")))->settings;

        $this->appendToJson('fields', Arrays::flattenKeysRecursively($_settings));
    }

    protected function postEmployeeChangePassword($view, $id = null)
    {
        $this->postChangePassword($view, $id);
    }

    protected function postStudentChangePassword($view, $id = null)
    {
        $this->postChangePassword($view, $id);
    }

    protected function postChangePassword($view, $id = null)
    {
        $repo = new Sync;
        $id = explode("_", $id);

        $random = General::convert(Helpers::input()->post('random')->getValue(), 'bool');
        $password = Helpers::input()->post('password')->getValue();

        if (!$random && (!Input::check($password) || Input::empty($password))) $this->setToast("Gelieve te kiezen voor een random wachtwoord of zelf een wachtwoord in te vullen!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            foreach ($id as $_id) {
                $item = Arrays::firstOrNull($repo->get($_id));

                if (!is_null($item->action) && $item->action !== "U") {
                    $this->setToast("Kan het wachtwoord van '{$item->linked->employee->formatted->fullNameReversed}' niet wijzigen!", self::VALIDATION_STATE_INVALID);
                    continue;
                }

                $item->action = "U";
                $item->password = ($random ? User::generatePassword() : $password);
                $item->setPassword = $item->password;

                $repo->set($item);

                $this->setToast("Het wachtwoord van '{$item->linked->employee->formatted->fullNameReversed}' wordt gewijzigd naar '{$item->password}'.");
            }

            $this->setCloseModal();
            $this->setReloadTable();
        }
    }

    protected function postSettings($view, $id = null)
    {
        $_settings = Helpers::input()->all();
        $settings = [];
        foreach ($_settings as $k => $v) $settings[str_replace("_", ".", $k)] = $v;
        $settings = General::normalizeArray($settings);

        $repo = new Navigation;
        $item = Arrays::first($repo->get(Session::get("moduleSettingsId")));
        $item->settings = array_replace_recursive($item->settings, $settings);

        $repo->set($item, ['settings']);
        $this->setToast("De instellingen zijn opgeslagen!");
    }

    protected function postUpdate($view, $id = null)
    {
        if (!$id) {
            $this->setError("No ID given...");
        } else {
            $repo = new Sync;

            $action = Helpers::input()->post("action")->getValue();
            $lastAction = Helpers::input()->post("lastAction")->getValue();
            $lastError = Helpers::input()->post("lastError")->getValue();
            $lastSync = Helpers::input()->post("lastSync")->getValue();

            if (Strings::isBlank($action)) $action = null;
            if (Strings::isBlank($lastAction)) $lastAction = null;
            if (Strings::isBlank($lastError)) $lastError = null;
            if (Strings::isBlank($lastSync)) $lastSync = null;

            $item = Arrays::first($repo->get($id));

            $item->action = $action;
            $item->lastAction = $lastAction;
            $item->lastError = $lastError;
            $item->lastSync = $lastSync;

            $repo->set($item);
        }
    }
}
