<?php

namespace Database\Object;

use Security\User;
use Router\Helpers;
use Helpers\Mapping;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Database\Repository\Module;
use Database\Repository\School;
use Database\Repository\LocalUser;
use Database\Interface\CustomObject;
use Database\Repository\ModuleSetting;

class Helpdesk extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"status",
		"priority",
		"creationDateTime",
		"schoolId",
		"creatorId",
		"assignedToId",
		"type",
		"subtype",
		"deviceName",
		"deviceLocation",
		"deviceBrand",
		"deviceType",
		"lastActionDateTime",
		"deleted"
	];

	public function init()
	{
		$moduleSettingsRepo = new ModuleSetting;
		$module = (new Module)->getByModule("helpdesk");
		$numberFormat = $moduleSettingsRepo->getByModuleAndKey($module->id, "format")->value;

		$this->typeFull = Mapping::get("helpdesk/type/{$this->type}");
		$this->subtypeFull = Mapping::get("helpdesk/subtype/{$this->subtype}");
		$this->priorityFull = Mapping::get("helpdesk/priority/{$this->priority}/description");
		$this->priorityColor = Mapping::get("helpdesk/priority/{$this->priority}/color");
		$this->statusFull = Mapping::get("helpdesk/status/{$this->status}/description");
		$this->statusColor = Mapping::get("helpdesk/status/{$this->status}/color");

		$this->number = $numberFormat;
		if (Strings::contains($this->number, "#")) {
			$count = substr_count($this->number, "#");
			$hashes = "";
			for ($i = 0; $i < $count; $i++) $hashes .= "#";
			$this->number = str_replace($hashes, str_pad($this->id, $count, 0, STR_PAD_LEFT), $this->number);
		}

		if (Strings::contains($this->number, "Y")) {
			$count = substr_count($this->number, "Y");
			$hashes = "";
			for ($i = 0; $i < $count; $i++) $hashes .= "Y";
			$this->number = str_replace($hashes, Clock::at($this->creationDateTime)->format($hashes), $this->number);
		}

		$this->subject = (Strings::isNotBlank($this->deviceName) ? "{$this->deviceName} - " : "") . $this->typeFull . (Strings::equal($this->type, "O") ? "" : " - {$this->subtypeFull}");

		$age = Clock::at($this->creationDateTime)->toDateTime()->diff(Clock::now()->toDateTime());
		if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0 && $age->i == 0) $this->age = $age->s . " seconden";
		else if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0) $this->age = $age->i . " minuten";
		else if ($age->y == 0 && $age->m == 0 && $age->d == 0) $this->age = $age->h . " uren";
		else if ($age->y == 0 && $age->m == 0) $this->age = $age->d . " dagen";
		else if ($age->y == 0) $this->age = $age->m . " maanden";
		else $this->age = $age->y . " jaren";

		$laage = Clock::at($this->lastActionDateTime)->toDateTime()->diff(Clock::now()->toDateTime());
		if ($laage->y == 0 && $laage->m == 0 && $laage->d == 0 && $laage->h == 0 && $laage->i == 0) $this->laage = $laage->s . " seconden";
		else if ($laage->y == 0 && $laage->m == 0 && $laage->d == 0 && $laage->h == 0) $this->laage = $laage->i . " minuten";
		else if ($laage->y == 0 && $laage->m == 0 && $laage->d == 0) $this->laage = $laage->h . " uren";
		else if ($laage->y == 0 && $laage->m == 0) $this->laage = $laage->d . " dagen";
		else if ($laage->y == 0) $this->laage = $laage->m . " maanden";
		$this->lastAction = Clock::at($this->lastActionDateTime)->format("d/m/Y H:i:s") . " ({$this->laage} geleden)";

		$this->formLocked = (($this->creatorId == User::getLoggedInUser()->id && $this->assignedToId != User::getLoggedInUser()->id) || Strings::equal($this->status, 'C'));
	}

	public function link()
	{
		$localUserRepo = new LocalUser;
		$this->school = (new School)->get($this->schoolId)[0];
		$this->creator = $localUserRepo->get(id: $this->creatorId)[0];
		$this->assignedTo = (is_null($this->assignedToId) ? false : $localUserRepo->get(id: $this->assignedToId)[0]);

		return $this;
	}
}
