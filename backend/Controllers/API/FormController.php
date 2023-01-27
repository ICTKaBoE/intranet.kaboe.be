<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Object\UserSecurity as ObjectUserSecurity;
use Database\Repository\CheckStudentRelationInsz;
use Database\Repository\Module;
use Database\Repository\ModuleSetting;
use Database\Repository\NoteScreenArticle;
use Database\Repository\NoteScreenPage;
use Database\Repository\Setting;
use Database\Repository\UserHomeWorkDistance;
use Database\Repository\UserProfile;
use Database\Repository\UserSecurity;
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

	public function settingsGeneral()
	{
		$_settings = (new Setting)->get();
		$settings = [];

		foreach ($_settings as $setting) $settings[$setting->id] = $setting->value;

		$this->appendToJson("fields", $settings);
		$this->handle();
	}

	public function settingsRights($prefix, $method, $id)
	{
		if ($method == "edit") $item = (new UserSecurity)->get($id)[0];
		else $item = new ObjectUserSecurity(['moduleId' => $id]);

		$this->appendToJson("fields", $item->toArray());
		$this->handle();
	}

	public function notescreenPages($prefix, $method, $id)
	{
		$this->appendToJson("fields", (new NoteScreenPage)->get($id)[0]->toArray());
		$this->handle();
	}

	public function notescreenArticles($prefix, $method, $id)
	{
		$this->appendToJson("fields", (new NoteScreenArticle)->get($id)[0]->toArray());
		$this->handle();
	}
}
