<?php

namespace Controllers\API;

use Security\User;
use Controllers\ApiController;
use Database\Repository\Module;
use Database\Repository\Setting;
use Database\Repository\Helpdesk;
use Database\Repository\UserProfile;
use Database\Repository\UserSecurity;
use Database\Repository\ModuleSetting;
use Database\Repository\ManagementRoom;
use Database\Repository\NoteScreenPage;
use Database\Repository\ManagementBeamer;
use Database\Repository\ManagementSwitch;
use Database\Repository\ManagementCabinet;
use Database\Repository\NoteScreenArticle;
use Database\Repository\ManagementBuilding;
use Database\Repository\ManagementComputer;
use Database\Repository\ManagementFirewall;
use Database\Repository\ManagementPatchpanel;
use Database\Repository\UserHomeWorkDistance;
use Database\Repository\ManagementAccesspoint;
use Database\Repository\CheckStudentRelationInsz;
use Database\Object\UserSecurity as ObjectUserSecurity;
use Database\Repository\Order;
use Database\Repository\OrderSupplier;
use Database\Repository\UserStart;

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

	public function getUserStart($prefix, $method, $id)
	{
		$this->appendToJson("fields", (new UserStart)->get($id)[0]->toArray());
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

	public function getSupervisionSettings()
	{
		$module = (new Module)->getByModule("supervision");
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

	public function helpdeskDetails($prefix, $id)
	{
		$details = (new Helpdesk)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function helpdeskSettings()
	{
		$module = (new Module)->getByModule("helpdesk");
		$settings = (new ModuleSetting)->getByModule($module->id);

		$returnSettings = [];
		foreach ($settings as $setting) $returnSettings[$setting->key] = $setting->value;

		$this->appendToJson("fields", $returnSettings);
		$this->handle();
	}

	public function managementBuilding($prefix, $method, $id)
	{
		$details = (new ManagementBuilding)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function managementRoom($prefix, $method, $id)
	{
		$details = (new ManagementRoom)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function managementCabinet($prefix, $method, $id)
	{
		$details = (new ManagementCabinet)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function managementPatchpanel($prefix, $method, $id)
	{
		$details = (new ManagementPatchpanel)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function managementFirewall($prefix, $method, $id)
	{
		$details = (new ManagementFirewall)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function managementSwitch($prefix, $method, $id)
	{
		$details = (new ManagementSwitch)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function managementAccesspoint($prefix, $method, $id)
	{
		$details = (new ManagementAccesspoint)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function managementComputer($prefix, $method, $id)
	{
		$details = (new ManagementComputer)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function managementBeamer($prefix, $method, $id)
	{
		$details = (new ManagementBeamer)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function managementPrinter($prefix, $method, $id)
	{
		$details = (new ManagementBeamer)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function order($prefix, $method, $id)
	{
		$details = (new Order)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function orderSupplier($prefix, $method, $id)
	{
		$details = (new OrderSupplier)->get($id)[0];
		$details->link();

		$this->appendToJson("fields", $details->toArray());
		$this->handle();
	}

	public function getSyncSettings()
	{
		$module = (new Module)->getByModule("synchronisation");
		$settings = (new ModuleSetting)->getByModule($module->id);

		$returnSettings = [];
		foreach ($settings as $setting) $returnSettings[$setting->key] = $setting->value;

		$this->appendToJson("fields", $returnSettings);
		$this->handle();
	}
}
