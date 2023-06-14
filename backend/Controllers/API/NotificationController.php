<?php

namespace Controllers\API;

use Security\User;
use Controllers\ApiController;
use Database\Repository\Notification;

class NotificationController extends ApiController
{
	public function get()
	{
		$this->appendToJson("notifications", (new Notification)->getByUserId(User::getLoggedInUser()->id));
		$this->handle();
	}

	public function read($prefix, $id)
	{
	}
}
