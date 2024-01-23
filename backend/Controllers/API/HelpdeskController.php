<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Security\Input;
use Helpers\Mapping;
use Mail\HelpdeskMail;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Log;
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
use Database\Repository\School;

class HelpdeskController extends ApiController
{
	const TEMPLATE_THREAD = '<div class="card mb-3">
								<div class="card-header card-header-light">
									<h3 class="card-title">
										{{thread:creator:fullName}}
										<span class="card-subtitle">{{thread:datetime}}</span>
									</h3>
								</div>

								<div class="card-body">{{thread:content}}</div>
							</div>';

	const TEMPLATE_ACTIONS = 	'<div class="list-group-item">
									<div class="row align-items-center">
										<div class="col">
											<div class="text-reset d-block">{{action:description}}</div>
											<div class="d-block text-muted mt-n1">{{action:datetime}}</div>
										</div>
									</div>
								</div>';

	// GET
	public function getDashboardStatus($view, $id = null)
	{
		if ($view == "chart") {
			$schoolRepo = new School;
			$helpdeskRepo = new Helpdesk;
			$allstatus = Mapping::get("helpdesk/status");

			$this->appendToJson(["xaxis", "categories"], Arrays::map($schoolRepo->get(), fn ($s) => $s->name));

			$series = [];

			foreach ($allstatus as $status) {
				$myarray = array("name" => array_values($status)[0]);
				array_push($series, $myarray);
			}

			foreach ($schoolRepo->get() as $idx => $school) {
				for ($i = 0; $i < count($allstatus); $i++) {
					$series[$i]["data"][$idx] = count($helpdeskRepo->getBySchoolByStatus($school->id, array_keys($allstatus)[$i]));
				}
			}
			$this->appendToJson("series", $series);
		}
		$this->handle();
	}

	public function getDashboardPriority($view, $id = null)
	{
		if ($view == "chart") {
			$schoolRepo = new School;
			$helpdeskRepo = new Helpdesk;
			$allpriority = Mapping::get("helpdesk/priority");

			$this->appendToJson(["xaxis", "categories"], Arrays::map($schoolRepo->get(), fn ($s) => $s->name));

			$series = [];

			foreach ($allpriority as $priority) {
				$myarray = array("name" => array_values($priority)[0]);
				array_push($series, $myarray);
			}

			foreach ($schoolRepo->get() as $idx => $school) {
				for ($i = 0; $i < count($allpriority); $i++) {
					$series[$i]["data"][$idx] = count($helpdeskRepo->getBySchoolByPriorityByNotStatus($school->id, array_keys($allpriority)[$i], "C"));
				}
			}
			$this->appendToJson("series", $series);
		}
		$this->handle();
	}

	public function getHelpdesk($view, $type, $id = null)
	{
		if ($view == "table") {
			$schoolId = Helpers::url()->getParam("school");
			$status = Helpers::url()->getParam("status");
			$priority = Helpers::url()->getParam("priority");

			$filters = [];

			if (Input::check($schoolId, Input::INPUT_TYPE_INT) && !Input::empty($schoolId)) $filters['schoolId'] = $schoolId;
			if (Input::check($status) && !Input::empty($status)) $filters['status'] = $status;
			if (Input::check($priority) && !Input::empty($priority)) $filters['priority'] = $priority;

			$rows = (new Helpdesk)->getByViewTypeWithFilters($type, $filters);
			$this->appendToJson(
				'columns',
				[
					[
						"type" => "checkbox",
						"class" => ["w-1"],
						"data" => "id"
					],
					[
						"title" => "Nummer",
						"data" => "number",
						"width" => 120
					],
					[
						"type" => "badge",
						"title" => "Status",
						"data" => "statusFull",
						"backgroundColor" => "statusColor",
						"width" => 150
					],
					[
						"type" => "badge",
						"title" => "School",
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 100
					],
					[
						"title" => "Leeftijd",
						"data" => "age",
						"width" => 150,
						"class" => ["d-none", "d-md-table-cell"]
					],
					[
						"title" => "Onderwerp",
						"data" => "subject",
					],
					[
						"type" => "badge",
						"title" => "Prioriteit",
						"data" => "priorityFull",
						"backgroundColor" => "priorityColor",
						"width" => 100
					],
					[
						"title" => "Aangemaakt door",
						"data" => "creator.fullName",
						"width" => 200
					],
					[
						"title" => "Toegewezen aan",
						"data" => "assignedTo.fullName",
						"width" => 200
					],
					[
						"title" => "Laatste activiteit",
						"data" => "lastAction",
						"width" => 300,
						"class" => ["d-none", "d-md-table-cell"]
					]
				]
			);

			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", $rows);
		} else if ($view == "form") $this->appendToJson(["fields"], Arrays::firstOrNull((new Helpdesk)->get($id)));

		$this->handle();
	}

	public function getThread($view, $id)
	{
		$threads = (new HelpdeskThread)->getByHelpdeskId($id);

		if ($view == "html") {
			$html = "";

			foreach ($threads as $thread) {
				$thread->link();
				$template = self::TEMPLATE_THREAD;

				foreach ($thread->toArray() as $key => $value) $template = str_replace("{{thread:{$key}}}", $value, $template);
				if (!is_null($thread->creator)) foreach ($thread->creator->toArray() as $key => $value) $template = str_replace("{{thread:creator:{$key}}}", $value, $template);
				$html .= $template;
			}

			$this->appendToJson("html", preg_replace("/{{.*?}}/", "", $html));
		}

		$this->handle();
	}

	public function getAction($view, $id)
	{
		$actions = (new HelpdeskAction)->getByHelpdeskId($id);

		if ($view == "html") {
			$html = "";

			foreach ($actions as $action) {
				$template = self::TEMPLATE_ACTIONS;

				foreach ($action as $key => $value) $template = str_replace("{{action:{$key}}}", $value, $template);
				$html .= $template;
			}

			$this->appendToJson(["html"], preg_replace("/{{.*?}}/", "", $html));
		}

		$this->handle();
	}

	public function getSettings($view)
	{
		$module = (new Module)->getByModule("helpdesk");
		$settings = (new ModuleSetting)->getByModule($module->id);

		$returnSettings = [];
		foreach ($settings as $setting) $returnSettings[$setting->key] = $setting->value;

		$this->appendToJson("fields", $returnSettings);
		$this->handle();
	}

	// POST
	public function postTicket()
	{
		$id = null;
		$schoolId = Helpers::input()->post('new_schoolId')->getValue();
		$priority = Helpers::input()->post('new_priority')->getValue();
		$type = Helpers::input()->post('new_type')->getValue();
		$subtype = Helpers::input()->post('new_subtype')?->getValue();
		$deviceLocation = Helpers::input()->post('new_deviceLocation')?->getValue();
		$deviceBrand = Helpers::input()->post('new_deviceBrand')?->getValue();
		$deviceType = Helpers::input()->post('new_deviceType')?->getValue();
		$deviceName = Helpers::input()->post('new_deviceName')?->getValue();
		$content = Helpers::input()->post('new_content')->getValue();

		if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
			$this->setValidation("new_schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
		}
		if (!Input::check($priority) || Input::empty($priority)) {
			$this->setValidation("new_priority", "Prioriteit moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			Log::write(type: Log::TYPE_ERROR, description: "Priority is not filled in");
		}
		if (!Input::check($type) || Input::empty($type)) {
			$this->setValidation("new_type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			Log::write(type: Log::TYPE_ERROR, description: "Type is not filled in");
		} else {
			if (Strings::equal($type, "L") || Strings::equal($type, "D")) {
				if (!Input::check($subtype) || Input::empty($subtype)) {
					$this->setValidation("new_subtype", "Sub-type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "Sub-type is not filled in");
				}
				if (!Input::check($deviceName) || Input::empty($deviceName)) {
					$this->setValidation("new_deviceName", "Toestelnaam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "Device name is not filled in");
				}
			} else if (Strings::equal($type, "B")) {
				if (!Input::check($deviceLocation) || Input::empty($deviceLocation)) {
					$this->setValidation("new_deviceLocation", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "Room is not filled in");
				}
				if (!Input::check($deviceBrand) || Input::empty($deviceBrand)) {
					$this->setValidation("new_deviceBrand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "Brand is not filled in");
				}
				if (!Input::check($deviceType) || Input::empty($deviceType)) {
					$this->setValidation("new_deviceType", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "Type is not filled in");
				}
			}
		}
		if (!Input::check($content) || Input::empty($content)) {
			$this->setValidation("new_content", "Probleem omschrijving moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			Log::write(type: Log::TYPE_ERROR, description: "Description of problem is not filled in");
		}

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
			$helpdesk->id = $id;
			$threadRepo->set(new ObjectHelpdeskThread([
				'helpdeskId' => $id,
				'creationDateTime' => $now,
				'creatorId' => $localUser->id,
				'content' => $content
			]));

			$actionRepo->set(new ObjectHelpdeskAction([
				'helpdeskId' => $id,
				'creationDateTime' => $now,
				'description' => $localUser->fullName . " created ticket"
			]));

			Log::write(description: "Created new ticket with id $id");
			(new HelpdeskMail)->sendCreationMail($helpdesk);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else {
			$this->setReloadTable();
			$this->setResetForm();
			$this->setCloseModal();
			$this->setToast("Helpdesk", "Uw ticket is opgeslagen.<br />Een ICT-medewerker zal zo dit zo spoedig mogelijk behandelen.");
		}
		$this->handle();
	}

	public function postThread($view)
	{
		$id = Helpers::input()->post("id")->getValue();
		$content = Helpers::input()->post('content')->getValue();

		if (!Input::check($content) || Input::empty($content)) {
			$this->setValidation("content", "Reactie moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			Log::write(type: Log::TYPE_ERROR, description: "Reaction is not filled in");
		}

		if ($this->validationIsAllGood() && $id) {
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
				'creatorId' => $localUser->id,
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

			Log::write(description: "Reacted on ticket with id $id");
			(new HelpdeskMail)->sendUpdateMail($helpdesk);
			(new HelpdeskMail)->sendUpdateAssignedToMail($helpdesk);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else {
			$this->setResetForm();
			$this->setCloseModal();
			$this->setReloadTable();
		}

		$this->handle();
	}

	public function postDetails($view, $id)
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

			Log::write(description: "Updated details ticket with id $id");
			if ($sendMailToAssignee) (new HelpdeskMail)->sendAssignMail($helpdesk);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else {
			$this->setReloadTable();
		}

		$this->handle();
	}

	public function postClaim($view, $type)
	{
		$ids = Helpers::input()->post('ids')->getValue();
		$ids = explode("-", $ids);

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
				'description' => $localUser->fullName . " claimed ticket."
			]));

			$this->setToast("Helpdesk", "U hebt #{$helpdesk->number} geclaimed!");
			Log::write(description: "Claimed ticket with id $_id");
			(new HelpdeskMail)->sendAssignMail($helpdesk);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else {
			$this->setReloadTable();
		}

		$this->handle();
	}

	public function postSettings()
	{
		$module = (new Module)->getByModule('helpdesk');
		$moduleSettingRepo = new ModuleSetting;

		foreach (DEFAULT_SETTINGS["helpdesk"] as $setting => $defaultValue) {
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

		Log::write(description: "Changed settings");

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else {
			$this->setToast("Helpdesk - Instellingen", "De instellingen zijn opgeslagen!");
		}
		$this->handle();
	}
}
