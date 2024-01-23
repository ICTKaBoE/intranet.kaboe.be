<?php

namespace Database\Object;

use Security\User;
use Helpers\CString;
use Helpers\Mapping;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Module;
use Database\Repository\School;
use Database\Repository\Supplier;
use Database\Repository\LocalUser;
use Database\Interface\CustomObject;
use Database\Repository\ModuleSetting;

class Order extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"creatorId",
		"acceptorId",
		"supplierId",
		"status",
		"description",
		"deleted"
	];

	public function init()
	{
		$moduleSettingsRepo = new ModuleSetting;
		$module = (new Module)->getByModule("orders");
		$numberFormat = $moduleSettingsRepo->getByModuleAndKey($module->id, "format")->value;

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

		$this->statusFull = Mapping::get("order/status/{$this->status}/description");
		$this->statusColor = Mapping::get("order/status/{$this->status}/color");

		$this->descriptionNoHtml = CString::noHtml($this->description);

		$this->_formLocked = 	($this->acceptorId == User::getLoggedInUser()->id && Arrays::contains(["A", "O", "S", "PR", "R"], $this->status)) ||
			($this->acceptorId != User::getLoggedInUser()->id && $this->creatorId != User::getLoggedInUser()->id) ||
			Arrays::contains(["C"], $this->status);
	}

	public function link()
	{
		$localuserRepo = new LocalUser;

		$this->school = (new School)->get($this->schoolId)[0];
		$this->creator = $localuserRepo->get($this->creatorId)[0];
		$this->acceptor = $localuserRepo->get($this->acceptorId)[0];
		$this->supplier = (new Supplier)->get($this->supplierId)[0];

		$this->supplier->link()->init();
	}
}
