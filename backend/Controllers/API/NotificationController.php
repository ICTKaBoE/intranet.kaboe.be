<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\Notification;
use Security\User;

class NotificationController extends ApiController
{
    // Get functions
    protected function getList($view, $id = null)
    {
        $items = (new Notification)->getByShowtimeNowByUserId(User::getLoggedInUser()->id);
        foreach ($items as $item) {
            $this->setToast($item->message, $item->type, $item->link, $item->delay);
        }
    }
}
