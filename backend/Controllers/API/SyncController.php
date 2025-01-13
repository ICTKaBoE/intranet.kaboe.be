<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\Sync;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;

class SyncController extends ApiController
{
    protected function getList($view, $id = null)
    {
        $repo = new Sync;

        if (Strings::equal($view, self::VIEW_PS)) {
            $items = $repo->get();
            // $items = Arrays::filter($items, fn($i) => !is_null($i->action));
            $this->appendToJson("items", Arrays::map($items, fn($i) => $i->toArray()));
        }
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

            if ($action == "C" || $action == "U" || $action == "E") {
                if (Strings::isNotBlank($item->emailAddress)) $item->setEmail = $item->emailAddress;
                if (Strings::isNotBlank($item->password)) $item->setPassword = $item->password;
            } else {
                $item->setEmail = null;
                $item->setPassword = null;
            }

            $item->action = $action;
            $item->lastAction = $lastAction;
            $item->lastError = $lastError;
            $item->lastSync = $lastSync;

            $repo->set($item);
        }
    }
}
