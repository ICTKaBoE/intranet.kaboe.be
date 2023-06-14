<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Helpers\Mapping;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Module;
use Database\Repository\School;
use Database\Repository\LocalUser;
use Database\Repository\UserAddress;
use Database\Repository\UserProfile;
use Database\Repository\ModuleSetting;
use Database\Repository\ManagementRoom;
use Database\Repository\NoteScreenPage;
use Database\Repository\ManagementCabinet;
use Database\Repository\ManagementBuilding;
use Database\Repository\ManagementComputer;
use Database\Repository\ManagementPatchpanel;
use Database\Repository\CheckStudentRelationInsz;
use Database\Repository\OrderSupplier;

class SelectController extends ApiController
{
	public function months()
	{
		$this->appendToJson("items", [
			[
				"id" => 1,
				"long" => "Januari",
				"short" => "Jan"
			],
			[
				"id" => 2,
				"long" => "Februari",
				"short" => "Feb"
			],
			[
				"id" => 3,
				"long" => "Maart",
				"short" => "Maa"
			],
			[
				"id" => 4,
				"long" => "April",
				"short" => "Apr"
			],
			[
				"id" => 5,
				"long" => "Mei",
				"short" => "Mei"
			],
			[
				"id" => 6,
				"long" => "Juni",
				"short" => "Jun"
			],
			[
				"id" => 7,
				"long" => "Juli",
				"short" => "Jul"
			],
			[
				"id" => 8,
				"long" => "Augustust",
				"short" => "Aug"
			],
			[
				"id" => 9,
				"long" => "September",
				"short" => "Sep"
			],
			[
				"id" => 10,
				"long" => "Oktober",
				"short" => "Okt"
			],
			[
				"id" => 11,
				"long" => "November",
				"short" => "Nov"
			],
			[
				"id" => 12,
				"long" => "December",
				"short" => "Dec"
			]
		]);
		$this->handle();
	}

	public function school()
	{
		$this->appendToJson("items", (new School)->get());
		$this->handle();
	}

	public function checkStudentRelationInszClass()
	{
		$school = Helpers::input()->get('parentValue');

		if (is_null($school)) $this->appendToJson('items', []);
		else {
			$items = (new CheckStudentRelationInsz)->getClassBySchool($school);
			$selectItems = [
				['name' => SELECT_ALL_VALUES]
			];

			foreach ($items as $index => $item) $selectItems[] = ['name' => $item];
			$items = Arrays::orderBy($items, "name");

			$this->appendToJson("items", $selectItems);
		}

		$this->handle();
	}

	public function userAddress()
	{
		$this->appendToJson("items", (new UserAddress)->getByUserId(User::getLoggedInUser()->id));
		$this->handle();
	}

	public function userStartType()
	{
		$_items = Mapping::get("user/start");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"name" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function modulesAssignRights()
	{
		$this->appendToJson("items", (new Module)->get());
		$this->handle();
	}

	public function users()
	{
		$this->appendToJson("items", Arrays::orderBy(Arrays::filter((new LocalUser)->get(), fn ($lu) => Strings::isNotBlank($lu->fullName)), "fullName"));
		$this->handle();
	}

	public function notescreenPages()
	{
		$this->appendToJson("items", (new NoteScreenPage)->getBySchoolId((new UserProfile)->getByUserId(User::getLoggedInUser()->id)->mainSchoolId));
		$this->handle();
	}

	public function helpdeskAssignToUsers()
	{
		$assignToIds = explode(";", (new ModuleSetting)->getByModuleAndKey((new Module)->getByModule("helpdesk")->id, "assignToIds")->value);
		$items = Arrays::filter((new LocalUser)->get(), fn ($lu) => Strings::isNotBlank($lu->fullName));

		$users = [];
		foreach ($items as $item) {
			if (Arrays::contains($assignToIds, $item->id)) $users[] = $item;
		}

		$this->appendToJson("items", Arrays::orderBy($users, "fullName"));
		$this->handle();
	}

	public function helpdeskPriority()
	{
		$_items = Mapping::get("helpdesk/priority");
		$items = [];

		foreach ($_items as $item => $rest) {
			$items[] = [
				"id" => $item,
				...$rest
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function helpdeskStatus()
	{
		$_items = Mapping::get("helpdesk/status");
		$items = [];

		foreach ($_items as $item => $rest) {
			$items[] = [
				"id" => $item,
				...$rest
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function helpdeskType()
	{
		$_items = Mapping::get("helpdesk/type");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function helpdeskSubtype()
	{
		$_items = Mapping::get("helpdesk/subtype");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function managementBuilding()
	{
		$schoolId = Helpers::input()->get('parentValue');

		$this->appendToJson("items", (new ManagementBuilding)->getBySchool($schoolId));
		$this->handle();
	}

	public function managementRoom()
	{
		$buildingId = Helpers::input()->get('parentValue');

		if (is_null($buildingId)) $this->appendToJson('items', []);
		else {
			$items = (new ManagementRoom)->getByBuilding($buildingId);
			$items = Arrays::orderBy($items, "fullNumber");
			$this->appendToJson("items", $items);
		}
		$this->handle();
	}

	public function managementCabinet()
	{
		$roomId = Helpers::input()->get('parentValue');

		if (is_null($roomId)) $this->appendToJson('items', []);
		else {
			$items = (new ManagementCabinet)->getByRoom($roomId);
			$this->appendToJson("items", $items);
		}
		$this->handle();
	}

	public function managementPatchpanel()
	{
		$cabinetId = Helpers::input()->get('parentValue');

		if (is_null($cabinetId)) $this->appendToJson('items', []);
		else {
			$items = (new ManagementPatchpanel)->getByCabinet($cabinetId);
			$this->appendToJson("items", $items);
		}
		$this->handle();
	}

	public function managementComputerType()
	{
		$_items = Mapping::get("management/computer/type");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function managementComputerOSType()
	{
		$_items = Mapping::get("management/computer/osType");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function managementComputerOSArchitecture()
	{
		$_items = Mapping::get("management/computer/osArchitecture");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function managementComputer()
	{
		$schoolId = Helpers::url()->getParam("schoolId");
		$type = Helpers::url()->getParam("type");

		$this->appendToJson("items", (new ManagementComputer)->getBySchoolAndType($schoolId, $type));
		$this->handle();
	}

	public function managementPrinterColorMode()
	{
		$_items = Mapping::get("management/printer/colormode");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function orderSupplier()
	{
		$this->appendToJson("items", (new OrderSupplier)->get());
		$this->handle();
	}
}
