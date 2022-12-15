<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\Module;
use Database\Repository\ModuleSetting;
use Database\Repository\UserHomeWorkDistance;
use Database\Repository\UserProfile;
use Router\Helpers;
use Security\User;

class FormController extends ApiController
{
	public function getDistance($prefix, $method, $id)
	{
		$this->appendToJson("fields", (new UserHomeWorkDistance)->get($id)[0]->toArray());
		$this->handle();
	}

	public function getProfile()
	{
		$profile = (new UserProfile)->getByUserId(User::getLoggedInUser()->id);
		if (!is_null($profile)) $profile->link()->toArray();
		$this->appendToJson("fields", $profile);
		$this->handle();
	}

	public function getBikeSettings()
	{
		$module = (new Module)->getByModule("bike");
		$settings = (new ModuleSetting)->getByModule($module->id);

		$returnSettings = [];
		foreach ($settings as $setting) $returnSettings[$setting->key] = $setting->value;

		$this->appendToJson("fields", $returnSettings);
		$this->handle();
	}
}
