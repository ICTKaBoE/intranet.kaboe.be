<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Log;
use Controllers\ApiController;
use Ouzo\Utilities\Comparator;
use Database\Repository\Module;
use Database\Repository\SyncStudent;
use Database\Repository\ModuleSetting;
use Database\Repository\SupplierContact;
use Database\Object\ModuleSetting as ObjectModuleSetting;

class SynchronisationController extends ApiController
{
	// GET
	public function getStudents($view, $what = null, $action = null, $id = null)
	{
		$school = Helpers::input()->get('school')?->getValue();
		$class = Helpers::input()->get('class')?->getValue();

		if ($view == "table") {
			$this->appendToJson(
				'columns',
				[
					[
						"type" => "checkbox",
						"class" => ["w-1"],
						"data" => "id"
					],
					[
						"type" => "badge",
						"title" => "School",
						"data" => "institute.school.name",
						"backgroundColorCustom" => "institute.school.color",
						"width" => 100
					],
					[
						"title" => "Klas",
						"data" => "class",
						"width" => 75
					],
					[
						"title" => "UID",
						"data" => "informatUID",
						"width" => 75
					],
					[
						"title" => "Naam",
						"data" => "name",
						"width" => 200
					],
					[
						"title" => "Voornaam",
						"data" => "firstName",
						"width" => 200
					],
					[
						"title" => "E-mail",
						"data" => "email"
					],
					[
						"type" => "password",
						"title" => "Wachtwoord",
						"data" => "password",
						"width" => 150,
						"format" => [
							"replace" => "*"
						]
					],
					[
						"title" => "Laatste sync",
						"data" => "lastAdSyncTime",
						"width" => 200
					],
					[
						"title" => "Laatste Foutmelding",
						"data" => "lastAdSyncError"
					]
				]
			);

			if (!is_null($school)) {
				$syncStudents = (new SyncStudent)->getBySchoolAndClass($school, $class, true);
				Arrays::each($syncStudents, fn ($s) => $s->link());
				$this->appendToJson(['rows'], Arrays::orderBy($syncStudents, "_orderfield"));
			} else $this->appendToJson('noRowsText', "Gelieve eerst te filteren");
		} else if ($view == "select") $this->appendToJson('items', (new SyncStudent)->getBySchoolAndClass($school, $class, true));

		$this->handle();
	}

	public function getSettings($view)
	{
		$module = (new Module)->getByModule("synchronisation");
		$settings = (new ModuleSetting)->getByModule($module->id);

		$returnSettings = [];
		foreach ($settings as $setting) $returnSettings[$setting->key] = $setting->value;

		$this->appendToJson("fields", $returnSettings);
		$this->handle();
	}

	// POST
	public function postSettings()
	{
		$module = (new Module)->getByModule('synchronisation');
		$moduleSettingRepo = new ModuleSetting;

		foreach (DEFAULT_SETTINGS["synchronisation"] as $setting => $defaultValue) {
			$moduleSetting = $moduleSettingRepo->getByModuleAndKey($module->id, $setting);
			$value = isset($_POST[$setting]) ? Helpers::input()->post($setting)->getValue() : $defaultValue;

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

		Log::write(description: "Changed settings for synchronisation");

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else {
			$this->setToast("Synchronisatie - Instellingen", "De instellingen zijn opgeslagen!");
		}
		$this->handle();
	}

	public function postResetPassword($view, $id)
	{
		$id = explode("-", $id);
		$random = Helpers::input()->post("random")?->getValue();

		$syncStudentRepo = new SyncStudent;

		foreach ($id as $_id) {
			$student = $syncStudentRepo->get($_id)[0];
			$password = $student->password;

			if (Strings::equal($random, "on")) {
				$password = $this->generatePassword();
			}

			$student->password = $password;
			$student->action = "UP";
			$syncStudentRepo->set($student);
		}

		$this->setReloadTable();
		$this->setCloseModal();
		$this->handle();
	}

	private function generatePassword()
	{
		$module = (new Module)->getByModule('synchronisation');
		$moduleSettingRepo = new ModuleSetting;
		$dictionary = $moduleSettingRepo->getByModuleAndKey($module->id, "dictionary")->value;
		$words = explode(PHP_EOL, $dictionary);

		$password = Arrays::randElement($words);
		$password .= str_pad(rand(0, pow(10, 2) - 1), 2, '0', STR_PAD_LEFT);

		return $password;
	}
}
