<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\Notification;
use Security\User;

class NotificationController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "notification";

    // Get functions
    protected function getList($view, $id = null)
    {
        $repo = new Notification;
        $items = $repo->getByShowtimeNowByUserId(User::getLoggedInUser()->id);
        foreach ($items as $item) {
            $this->setToast($item->message, $item->type, $item->link, $item->delay);
            $item->deleted = true;
            $repo->set($item);
        }
    }
}
