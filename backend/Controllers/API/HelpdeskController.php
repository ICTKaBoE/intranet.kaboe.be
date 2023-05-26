<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\Mapping;
use Mail\HelpdeskMail;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Module;
use Database\Repository\Helpdesk;
use Database\Repository\ModuleSetting;
use Database\Repository\HelpdeskAction;
use Database\Repository\HelpdeskThread;
use Database\Object\Helpdesk as ObjectHelpdesk;
use Database\Object\HelpdeskAction as ObjectHelpdeskAction;
use Database\Object\HelpdeskThread as ObjectHelpdeskThread;
use Database\Object\ModuleSetting as ObjectModuleSetting;
use Ouzo\Utilities\Arrays;

class HelpdeskController extends ApiController
{
	public function details($prefix, $id)
	{
		$priority = Helpers::input()->post('priority')->getValue();
		$status = Helpers::input()->post('status')->getValue();
		$type = Helpers::input()->post('type')->getValue();
		$subtype = Helpers::input()->post('subtype')->getValue();
		$assignedToId = Helpers::input()->post('assignedToId')->getValue();

		if ($this->validationIsAllGood()) {
			$now = Clock::nowAsString("Y-m-d H:i:s");
			$localUser = User::getLoggedInUser();
			$repo = new Helpdesk;
			$actionRepo = new HelpdeskAction;
			$detailsDescriptionExtra = "";
			$sendMailToAssignee = false;

			$helpdesk = $repo->get($id)[0];

			if (!Strings::equal($priority, $helpdesk->priority)) $detailsDescriptionExtra .= "<br />Prioriteit gewijzigd ({$helpdesk->priorityFull} -> " . Mapping::get("helpdesk/priority/{$priority}/description") . ")";
			if (!Strings::equal($status, $helpdesk->status)) $detailsDescriptionExtra .= "<br />Status gewijzigd ({$helpdesk->statusFull} -> " . Mapping::get("helpdesk/status/{$status}/description") . ")";
			if (!Strings::equal($type, $helpdesk->type)) $detailsDescriptionExtra .= "<br />Type gewijzigd ({$helpdesk->typeFull} -> " . Mapping::get("helpdesk/type/{$type}") . ")";
			if (!Strings::equal($subtype, $helpdesk->subtype)) $detailsDescriptionExtra .= "<br />Sub-type gewijzigd ({$helpdesk->subtypeFull} -> " . Mapping::get("helpdesk/subtype/{$subtype}") . ")";
			if (!Strings::equal($assignedToId, $helpdesk->assignedToId)) {
				$detailsDescriptionExtra .= "<br />Toegewezen aan gewijzigd";
				$sendMailToAssignee = true;
			}

			$helpdesk->priority = $priority;
			$helpdesk->status = $status;
			$helpdesk->type = $type;
			$helpdesk->subtype = $subtype;
			$helpdesk->assignedToId = $assignedToId;
			$helpdesk->lastActionDateTime = $now;

			$repo->set($helpdesk);
			$actionRepo->set(new ObjectHelpdeskAction([
				'helpdeskId' => $id,
				'creationDateTime' => $now,
				'description' => $localUser->fullName . " updated details" . $detailsDescriptionExtra
			]));

			$helpdesk->link();
			if ($sendMailToAssignee) (new HelpdeskMail)->sendAssignMail($helpdesk->assignedTo->username, $helpdesk->assignedTo->fullName, $helpdesk->number);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->setReload();

		$this->handle();
	}

	public function thread($prefix, $id)
	{
		$content = Helpers::input()->post('content')->getValue();

		if (!Input::check($content) || Input::empty($content)) $this->setValidation("content", "Reactie moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

		if ($this->validationIsAllGood()) {
			$now = Clock::nowAsString("Y-m-d H:i:s");
			$localUser = User::getLoggedInUser();
			$detailsDescriptionExtra = "";
			$closed = false;

			$repo = new Helpdesk;
			$actionRepo = new HelpdeskAction;
			$threadRepo = new HelpdeskThread;

			$helpdesk = $repo->get($id)[0];
			$helpdesk->lastActionDateTime = $now;

			if (Strings::equal($helpdesk->status, 'C')) {
				$closed = true;
				$detailsDescriptionExtra .= "<br />Status gewijzigd ({$helpdesk->statusFull} -> " . Mapping::get("helpdesk/status/O/description") . ")";
				$helpdesk->status = 'O';
			}

			$repo->set($helpdesk);
			$threadRepo->set(new ObjectHelpdeskThread([
				'helpdeskId' => $id,
				'creationDateTime' => $now,
				'creatorId' => $localUser->fullName,
				'content' => $content
			]));

			if ($closed) {
				$actionRepo->set(new ObjectHelpdeskAction([
					'helpdeskId' => $id,
					'creationDateTime' => $now,
					'description' => $localUser->fullName . " updated details" . $detailsDescriptionExtra
				]));
			}

			$actionRepo->set(new ObjectHelpdeskAction([
				'helpdeskId' => $id,
				'creationDateTime' => $now,
				'description' => $localUser->fullName . " posted thread"
			]));

			$helpdesk->link();
			if ($helpdesk->creatorId != $localUser->id) (new HelpdeskMail)->sendUpdateMail($helpdesk->creator->username, $helpdesk->creator->fullName, $helpdesk->number);
			if ($helpdesk->assignedToId != 0 && $helpdesk->assignedToId != $localUser->id) (new HelpdeskMail)->sendUpdateAssignedToMail($helpdesk->assignedTo->username, $helpdesk->assignedTo->fullName, $helpdesk->number);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->setReload();

		$this->handle();
	}

	public function newTicket($prefix)
	{
		$id = null;
		$schoolId = Helpers::input()->post('schoolId')->getValue();
		$priority = Helpers::input()->post('priority')->getValue();
		$type = Helpers::input()->post('type')->getValue();
		$subtype = Helpers::input()->post('subtype')?->getValue();
		$deviceLocation = Helpers::input()->post('deviceLocation')?->getValue();
		$deviceBrand = Helpers::input()->post('deviceBrand')?->getValue();
		$deviceType = Helpers::input()->post('deviceType')?->getValue();
		$deviceName = Helpers::input()->post('deviceName')?->getValue();
		$content = Helpers::input()->post('content')->getValue();
		$new = Helpers::input()->post('new')?->getValue();

		if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($content)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		if (!Input::check($priority) || Input::empty($priority)) $this->setValidation("priority", "Prioriteit moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		if (!Input::check($type) || Input::empty($type)) $this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		else {
			if (Strings::equal($type, "L") || Strings::equal($type, "D")) {
				if (!Input::check($subtype) || Input::empty($subtype)) $this->setValidation("subtype", "Sub-type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				if (!Input::check($deviceName) || Input::empty($deviceName)) $this->setValidation("deviceName", "Toestelnaam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			} else if (Strings::equal($type, "B")) {
				if (!Input::check($deviceLocation) || Input::empty($deviceLocation)) $this->setValidation("deviceLocation", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				if (!Input::check($deviceBrand) || Input::empty($deviceBrand)) $this->setValidation("deviceBrand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				if (!Input::check($deviceType) || Input::empty($deviceType)) $this->setValidation("deviceType", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			}
		}
		if (!Input::check($content) || Input::empty($content)) $this->setValidation("content", "Probleem omschrijving moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

		if ($this->validationIsAllGood()) {
			$now = Clock::nowAsString("Y-m-d H:i:s");
			$localUser = User::getLoggedInUser();

			$repo = new Helpdesk;
			$actionRepo = new HelpdeskAction;
			$threadRepo = new HelpdeskThread;

			$helpdesk = new ObjectHelpdesk([
				"status" => "N",
				"priority" => $priority,
				"creationDateTime" => $now,
				"schoolId" => $schoolId,
				"creatorId" => $localUser->id,
				"assignedToId" => null,
				"type" => $type,
				"subtype" => $subtype,
				"deviceName" => $deviceName,
				"deviceLocation" => $deviceLocation,
				"deviceBrand" => $deviceBrand,
				"deviceType" => $deviceType,
				"lastActionDateTime" => $now,
			]);

			$id = $repo->set($helpdesk);
			$threadRepo->set(new ObjectHelpdeskThread([
				'helpdeskId' => $id,
				'creationDateTime' => $now,
				'creatorId' => $localUser->fullName,
				'content' => $content
			]));

			$actionRepo->set(new ObjectHelpdeskAction([
				'helpdeskId' => $id,
				'creationDateTime' => $now,
				'description' => $localUser->fullName . " created ticket"
			]));

			$helpdesk->init();
			$helpdesk->link();
			(new HelpdeskMail)->sendCreationMail($helpdesk->creator->username, $helpdesk->creator->fullName, $helpdesk->number);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->setRedirect((is_null($new) ? (is_null($id) ? "/{$prefix}/helpdesk/mine" : "/{$prefix}/helpdesk/details?id={$id}") : "/{$prefix}/helpdesk/mine/new"));

		$this->handle();
	}

	public function claim($prefix, $id)
	{
		$ids = explode("-", $id);

		$repo = new Helpdesk;
		$actionRepo = new HelpdeskAction;
		$localUser = User::getLoggedInUser();

		foreach ($ids as $_id) {
			$helpdesk = $repo->get($_id)[0];
			$helpdesk->assignedToId = $localUser->id;

			$repo->set($helpdesk);

			$actionRepo->set(new ObjectHelpdeskAction([
				'helpdeskId' => $_id,
				'creationDateTime' => Clock::nowAsString("Y-m-d H:i:s"),
				'description' => $localUser->fullName . " updated details<br />Toegewezen aan gewijzigd"
			]));
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->setReload();
		$this->handle();
	}

	public function settings()
	{
		$settings = [
			"format" => "T-ST-######",
			"assignToIds" => "",
			"informNewToIds" => ""
		];

		$module = (new Module)->getByModule('helpdesk');
		$moduleSettingRepo = new ModuleSetting;

		foreach ($settings as $setting => $defaultValue) {
			$moduleSetting = $moduleSettingRepo->getByModuleAndKey($module->id, $setting);
			$value = isset($_POST[$setting]) ? Helpers::input()->post($setting)->getValue() : $defaultValue;

			if (is_array($value)) {
				$value = Arrays::map($value, fn ($v) => $v->getValue());
				$value = implode(";", $value);
			}

			if (is_null($moduleSetting)) {
				$moduleSetting = new ObjectModuleSetting([
					'moduleId' => $module->id,
					'key' => $setting,
					'value' => Input::convertToBool($value)
				]);
			} else {
				$moduleSetting->value = Input::convertToBool($value);
			}

			$moduleSettingRepo->set($moduleSetting);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		}
		$this->handle();
	}
}
