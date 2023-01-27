<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Object\UserSecurity as ObjectUserSecurity;
use Database\Repository\Setting;
use Database\Repository\UserSecurity;
use Ouzo\Utilities\Strings;
use Router\Helpers;

class SettingsController extends ApiController
{
	public function general()
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

		$this->handle();
	}

	public function rights($prefix, $method, $id)
	{
		$moduleId = Helpers::input()->post('moduleId')->getValue();
		$userId = is_null(Helpers::input()->post("userId")) ? false : Helpers::input()->post("userId")->getValue();
		$view = is_null(Helpers::input()->post("view")) ? false : Helpers::input()->post("view")->getValue();
		$edit = is_null(Helpers::input()->post("edit")) ? false : Helpers::input()->post("edit")->getValue();
		$export = is_null(Helpers::input()->post("export")) ? false : Helpers::input()->post("export")->getValue();
		$changeSettings = is_null(Helpers::input()->post("changeSettings")) ? false : Helpers::input()->post("changeSettings")->getValue();

		$view = Strings::equal($view, "on");
		$edit = Strings::equal($edit, "on");
		$export = Strings::equal($export, "on");
		$changeSettings = Strings::equal($changeSettings, "on");

		$repo = new UserSecurity;
		$item = $method == 'edit' ? $repo->get($id)[0] : new ObjectUserSecurity;

		$item->moduleId = $moduleId;
		$item->userId = $userId;
		$item->view = $view;
		$item->edit = $edit;
		$item->export = $export;
		$item->changeSettings = $changeSettings;

		$repo->set($item);
		$this->setRedirect("/{$prefix}/settings/rights");
		$this->handle();
	}

	public function deleteRights($prefix, $id)
	{
		$repo = new UserSecurity;
		$ids = explode("-", $id);

		foreach ($ids as $_id) {
			$item = $repo->get($_id)[0];
			$item->deleted = true;

			$repo->set($item);
		}

		$this->setReload();
		$this->handle();
	}
}
