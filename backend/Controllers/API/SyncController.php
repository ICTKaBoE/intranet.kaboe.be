<?php

namespace Controllers\API;

use Helpers\Form;
use Helpers\Table;
use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Sync\Sync;
use Database\Repository\Navigation\TableDef;
use Database\Repository\Navigation\Navigation;

class SyncController extends ApiController
{

    protected function getEmployee($view, $id = null)
    {
        $repo = new Sync;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                "type" => "E"
            ];

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", array_values($items));
        }
    }

    protected function getStudent($view, $id = null)
    {
        $repo = new Sync;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [
                "type" => "S"
            ];

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", array_values($items));
        }
    }

    protected function getList($view, $id = null, $type = null)
    {
        $repo = new Sync;

        if (Strings::equal($view, self::VIEW_PS)) {
            $items = $repo->get();
            $items = Arrays::orderBy($items, "type");
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray()));
        }
    }

    protected function getSettings($view, $id = null)
    {
        $this->getNavigationSettings("sync");
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

        $_fields = [
            "random" => ["convert" => "bool"],
            "password" => ["type" => "string", "mandatory" => true, "preconditions" => ["random" => false]]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            foreach ($id as $_id) {
                $item = $repo->getById($_id);

                if (!is_null($item->action) && $item->action !== "U") {
                    $this->setToast("Kan het wachtwoord van '{$item->linked->employee->formatted->fullNameReversed}' niet wijzigen!", self::VALIDATION_STATE_INVALID);
                    continue;
                }

                $item->fillWithPostData();
                $item->action = "U";
                if ($fields['random']) $item->password = User::generatePassword();
                $item->setPassword = $item->password;

                $repo->set($item);

                $this->setToast("Het wachtwoord van '{$item->linked->employee->formatted->fullNameReversed}' wordt gewijzigd naar '{$item->password}'.");
            }

            $this->setCloseModal();
            $this->setReloadTable();
            $this->setResetForm();
        }
    }

    protected function postSettings($view, $id = null)
    {
        $this->postNavigationSettings("sync");
    }

    protected function postUpdate($view, $id = null)
    {
        if (!$id) {
            $this->setError("No ID given...");
        } else {
            $_fields = [
                "action" => ["default" => null],
                "lastAction" => ["default" => null],
                "lastError" => ["default" => null],
                "lastSync" => ["default" => null],
            ];

            [$invalid, $fields] = Form::Validate($_fields);
            Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

            $repo = new Sync;

            $item = $repo->getById($id);
            $item->fillWithPostData($fields);

            $repo->set($item);
        }
    }
}
