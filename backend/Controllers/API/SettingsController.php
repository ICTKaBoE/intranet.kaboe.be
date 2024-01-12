<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Log;
use Controllers\ApiController;
use Database\Object\UserSecurity as ObjectUserSecurity;
use Database\Repository\Module;
use Database\Repository\Setting;
use Database\Repository\LocalUser;
use Database\Repository\UserSecurity;
use Helpers\CString;

class SettingsController extends ApiController
{
	// GET
	public function getGeneral($view)
	{
		$_settings = (new Setting)->get();
		$settings = [];

		foreach ($_settings as $setting) $settings[$setting->id] = $setting->value;

		if ($view == "form") $this->appendToJson(["fields"], $settings);

		$this->handle();
	}

	public function getRights($view, $id = null)
	{
		$userSecurity = (new UserSecurity)->get();
		$users = (new LocalUser)->get();
		$modules = (new Module)->get();

		if ($view == "table") {
			$columns = [
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "Naam",
					"data" => "fullName"
				],
			];

			foreach ($modules as $module) {
				$columns[] = [
					"title" => $module->name,
					"titleIcon" => $module->icon,
					"data" => "rightIcons{$module->id}",
					"class" => ["w-1"]
				];

				Arrays::each($users, function ($user) use ($userSecurity, $module) {
					$rights = Arrays::firstOrNull(Arrays::filter($userSecurity, fn ($us) => ($us->moduleId == 0 && $us->userId == $user->id)));

					if (!$rights) $rights = Arrays::firstOrNull(Arrays::filter($userSecurity, fn ($us) => ($us->moduleId == $module->id && $us->userId == $user->id)));
					$maxRightIcon = ($rights->locked ? 'lock' : ($rights->changeSettings ? 'settings' : ($rights->export ? 'file-export' : ($rights->edit ? 'pencil' : ($rights->view ? 'eye' : false)))));
					$name = "rightIcons{$module->id}";
					$user->$name = "<i class='icon ti ti-" . ($maxRightIcon ? $maxRightIcon : 'x') . " text-" . ($maxRightIcon ? 'green' : 'red') . "'></i>";

					return $user;
				});
			}

			$this->appendToJson('columns', $columns);
			$this->appendToJson('rows', $users);
		} else if ($view == "form") {
			$this->appendToJson('fields', Arrays::firstOrNull((new UserSecurity)->get($id)));
		}

		$this->handle();
	}

	public function getLogs($view)
	{
		$logs = (new Log)->get();
		Arrays::each($logs, fn ($l) => $l->link());

		if ($view == "table") {
			$columns = [
				[
					"type" => "badge",
					"title" => "Type",
					"data" => "typeFull",
					"backgroundColorCustom" => "typeColor",
					"width" => 50
				],
				[
					"title" => "Datum/Tijd",
					"data" => "datetime",
					"width" => 175
				],
				[
					"title" => "Route",
					"data" => "route",
					"width" => 200
				],
				[
					"title" => "Gebruiker",
					"data" => "user.fullName",
					"width" => 200
				],
				[
					"title" => "Melding",
					"data" => "description"
				]
			];

			$this->appendToJson('columns', $columns);
			$this->appendToJson('rows', $logs);
		}

		$this->handle();
	}

	// POST
	public function postSettings($view)
	{
		$repo = new Setting;
		$settings = $repo->get();

		foreach ($settings as $setting) {
			$post = Helpers::input()->post(str_replace(".", "_", $setting->id));
			if (is_null($post)) {
				if ($setting->type == "switch") $post = "0";
				else $post = $setting->value;
			} else $post = $post->getValue();

			$setting->value = $post;
			$repo->set($setting);
		}

		$this->setToast("Instellingen - Algemeen", "De instellingen zijn opgeslagen!");
		$this->handle();
	}

	public function postRights($view, $userId = null)
	{
		// $userId = Helpers::input()->post('userId')->getValue();
		$moduleIds = Helpers::input()->post('moduleIds')?->getValue();
		$view = Helpers::input()->post('_view')->getValue();
		$edit = Helpers::input()->post('_edit')->getValue();
		$export = Helpers::input()->post('_export')->getValue();
		$changeSettings = Helpers::input()->post('_changeSettings')->getValue();
		$locked = Helpers::input()->post('_locked')->getValue();

		if (!Input::check($moduleIds) || Input::empty($moduleIds)) {
			$this->setValidation("moduleIds", "Module(s) moeten aangeduid zijn!", self::VALIDATION_STATE_INVALID);
			Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
		}

		if ($this->validationIsAllGood()) {
			$repo = new UserSecurity;

			foreach (explode(";", $moduleIds) as $moduleId) {
				$us = $repo->getByUserAndModule($userId, $moduleId) ?? new ObjectUserSecurity;
				$us->moduleId = $moduleId;
				$us->userId = $userId;
				$us->view = Input::convertToBool($view);
				$us->edit = Input::convertToBool($edit);
				$us->export = Input::convertToBool($export);
				$us->changeSettings = Input::convertToBool($changeSettings);
				$us->locked = Input::convertToBool($locked);

				$repo->set($us);
				Log::write(description: "Added/Edited user security for user {$userId} and module {$moduleId}");
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
			$this->setToast("Instellingen - Gebruikersrechten", "De rechten zijn opgeslagen!");
		}

		$this->handle();
	}
}
