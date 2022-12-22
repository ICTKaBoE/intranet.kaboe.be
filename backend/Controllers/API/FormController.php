<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\CheckStudentRelationInsz;
use Database\Repository\Module;
use Database\Repository\ModuleSetting;
use Database\Repository\UserHomeWorkDistance;
use Database\Repository\UserProfile;
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

	public function getCheckStudentRelationInsz($prefix, $method, $id)
	{
		$check = (new CheckStudentRelationInsz)->get($id)[0];
		$check->check();

		if (!$check->childInszIsCorrect) $this->setValidation("childInsz", state: self::VALIDATION_STATE_INVALID);
		if (!$check->motherInszIsCorrect) $this->setValidation("motherInsz", state: self::VALIDATION_STATE_INVALID);
		if (!$check->fatherInszIsCorrect) $this->setValidation("fatherInsz", state: self::VALIDATION_STATE_INVALID);

		$this->appendToJson("fields", $check->toArray());
		$this->handle();
	}
}
