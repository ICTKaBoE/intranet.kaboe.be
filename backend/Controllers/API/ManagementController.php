<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Security\Input;
use Management\Management;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Log;
use Controllers\ApiController;
use Database\Repository\LocalUser;
use Database\Repository\ManagementCart;
use Database\Repository\ManagementIpad;
use Database\Repository\ManagementRoom;
use Database\Repository\ManagementBeamer;
use Database\Repository\ManagementSwitch;
use Database\Repository\ManagementCabinet;
use Database\Repository\ManagementPrinter;
use Database\Repository\ManagementBuilding;
use Database\Repository\ManagementComputer;
use Database\Repository\ManagementFirewall;
use Database\Repository\ManagementPatchpanel;
use Database\Repository\ManagementAccesspoint;
use Database\Object\ManagementRoom as ObjectManagementRoom;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\HLookup;
use Database\Object\ManagementBeamer as ObjectManagementBeamer;
use Database\Object\ManagementSwitch as ObjectManagementSwitch;
use Database\Object\ManagementCabinet as ObjectManagementCabinet;
use Database\Object\ManagementPrinter as ObjectManagementPrinter;
use Database\Object\ManagementBuilding as ObjectManagementBuilding;
use Database\Object\ManagementComputer as ObjectManagementComputer;
use Database\Object\ManagementFirewall as ObjectManagementFirewall;
use Database\Object\ManagementPatchpanel as ObjectManagementPatchpanel;
use Database\Object\ManagementAccesspoint as ObjectManagementAccesspoint;
use Database\Object\ManagementCart as ObjectManagementCart;
use Database\Object\ManagementIpad as ObjectManagementIpad;
use Database\Repository\School;
use Security\Code;

class ManagementController extends ApiController
{
	// GET
	public function getDashboard($view, $id = null)
	{
		if ($view == "chart") {
			$schoolRepo = new School;
			$buildingRepo = new ManagementBuilding;
			$roomsRepo = new ManagementRoom;
			$cabinetRepo = new ManagementCabinet;
			$patchpanelRepo = new ManagementPatchpanel;
			$firewallRepo = new ManagementFirewall;
			$switchRepo = new ManagementSwitch;
			$accesspointRepo = new ManagementAccesspoint;
			$computerRepo = new ManagementComputer;
			$ipadRepo = new ManagementIpad;
			$beamerRepo = new ManagementBeamer;
			$printerRepo = new ManagementPrinter;

			$this->appendToJson(["xaxis", "categories"], Arrays::map($schoolRepo->get(), fn ($s) => $s->name));
			$series = [
				[
					"name" => "Gebouwen"
				],
				[
					"name" => "Lokalen"
				],
				[
					"name" => "Netwerkkasten"
				],
				[
					"name" => "Patchpanelen"
				],
				[
					"name" => "Firewalls"
				],
				[
					"name" => "Switches"
				],
				[
					"name" => "Access Points"
				],
				[
					"name" => "Computers"
				],
				[
					"name" => "Ipads"
				],
				[
					"name" => "Beamers"
				],
				[
					"name" => "Printers"
				]
			];

			foreach ($schoolRepo->get() as $idx => $school) {
				$series[0]["data"][$idx] = count($buildingRepo->getBySchool($school->id));
				$series[1]["data"][$idx] = count($roomsRepo->getBySchool($school->id));
				$series[2]["data"][$idx] = count($cabinetRepo->getBySchool($school->id));
				$series[3]["data"][$idx] = count($patchpanelRepo->getBySchool($school->id));
				$series[4]["data"][$idx] = count($firewallRepo->getBySchool($school->id));
				$series[5]["data"][$idx] = count($switchRepo->getBySchool($school->id));
				$series[6]["data"][$idx] = count($accesspointRepo->getBySchool($school->id));
				$series[7]["data"][$idx] = count($computerRepo->getBySchool($school->id));
				$series[8]["data"][$idx] = count($ipadRepo->getBySchool($school->id));
				$series[9]["data"][$idx] = count($beamerRepo->getBySchool($school->id));
				$series[10]["data"][$idx] = count($printerRepo->getBySchool($school->id));
			}
			$this->appendToJson("series", $series);
		}
		$this->handle();
	}

	public function getBuilding($view, $id = null)
	{
		if ($view == "select") {
			$schoolId = Helpers::input()->get("parentValue");

			$this->appendToJson("items", (new ManagementBuilding)->getBySchool($schoolId));
		} else if ($view == "table") {
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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Naam",
						"data" => "name"
					]
				]
			);
			$rows = (new ManagementBuilding)->get();
			Arrays::each($rows, fn ($row) => $row->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementBuilding)->get($id)));
		$this->handle();
	}

	public function getRoom($view, $id = null)
	{
		if ($view == "select") {
			$buildingId = Helpers::input()->get("parentValue");
			$schoolId = Helpers::input()->get("schoolId");

			$items = [];

			if (is_null($buildingId) && is_null($schoolId)) $items = (new ManagementRoom)->get($id);
			else if (!is_null($buildingId) && is_null($schoolId)) $items = (new ManagementRoom)->getByBuilding($buildingId);
			else if (is_null($buildingId) && !is_null($schoolId)) $items = (new ManagementRoom)->getBySchool($schoolId);

			Arrays::each($items, fn ($i) => $i->link()->init());
			Arrays::orderBy($items, "_orderField");
			$this->appendToJson("items", $items);
		} else if ($view == "table") {
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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Gebouw",
						"data" => "building.name",
						"width" => 100
					],
					[
						"title" => "Nummer",
						"data" => "fullNumber"
					]
				]
			);
			$rows = (new ManagementRoom)->get();
			Arrays::each($rows, fn ($row) => $row->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementRoom)->get($id)));
		$this->handle();
	}


	public function getCabinet($view, $id = null)
	{
		if ($view == "select") {
			$roomId = Helpers::input()->get("parentValue");

			$this->appendToJson("items", (new ManagementCabinet)->getByRoom($roomId));
		} else if ($view == "table") {

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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Gebouw",
						"data" => "building.name",
						"width" => 100
					],
					[
						"title" => "Lokaal",
						"data" => "room.fullNumber",
						"width" => 100
					],
					[
						"title" => "Naam",
						"data" => "name"
					]
				]
			);
			$rows = (new ManagementCabinet)->get();
			Arrays::each($rows, fn ($row) => $row->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementCabinet)->get($id)));
		$this->handle();
	}

	public function getPatchPanel($view, $id = null)
	{
		if ($view == "select") {
			$cabinetId = Helpers::input()->get("parentValue");

			$this->appendToJson("items", (new ManagementPatchpanel)->getByCabinet($cabinetId));
		} else if ($view == "table") {

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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Gebouw",
						"data" => "building.name",
						"width" => 100
					],
					[
						"title" => "Lokaal",
						"data" => "room.fullNumber",
						"width" => 100
					],
					[
						"title" => "Netwerkkast",
						"data" => "cabinet.name",
						"width" => 100
					],
					[
						"title" => "Naam",
						"data" => "name"
					],
					[
						"title" => "Aantal patchpunten",
						"data" => "ports"
					]
				]
			);
			$rows = (new ManagementPatchpanel)->get();
			Arrays::each($rows, fn ($row) => $row->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementPatchpanel)->get($id)));
		$this->handle();
	}

	public function getFirewall($view, $id = null)
	{
		if ($view == "select") {
		} else if ($view == "table") {
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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Gebouw",
						"data" => "building.name",
						"width" => 100
					],
					[
						"title" => "Lokaal",
						"data" => "room.fullNumber",
						"width" => 100
					],
					[
						"title" => "Netwerkkast",
						"data" => "cabinet.name",
						"width" => 100
					],
					[
						"title" => "Hostnaam",
						"data" => "hostname",
						"width" => 150
					],
					[
						"title" => "Merk",
						"data" => "brand",
						"width" => 150
					],
					[
						"title" => "Model",
						"data" => "model",
						"width" => 150
					],
					[
						"title" => "Serienummer",
						"data" => "serialnumber",
						"width" => 150
					],
					[
						"title" => "MAC Adres",
						"data" => "macaddress",
						"width" => 150
					],
					[
						"title" => "Firmware",
						"data" => "firmware",
						"width" => 200
					],
					[
						"type" => "url",
						"title" => "Beheerlink",
						"data" => "interface"
					],
					[
						"title" => "Gebruikersnaam",
						"data" => "username"
					],
					[
						"type" => "password",
						"title" => "Wachtwoord",
						"data" => "password",
						"width" => 150,
						"format" => [
							"replace" => "*"
						]
					]
				]
			);
			$rows = (new ManagementFirewall)->get();
			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementFirewall)->get($id)));
		$this->handle();
	}

	public function getSwitch($view, $id = null)
	{
		if ($view == "select") {
			$switchId = Helpers::input()->get("parentValue");

			$this->appendToJson("items", (new ManagementSwitch)->getBySchool($switchId));
		} else if ($view == "table") {
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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Gebouw",
						"data" => "building.name",
						"width" => 100
					],
					[
						"title" => "Lokaal",
						"data" => "room.fullNumber",
						"width" => 100
					],
					[
						"title" => "Netwerkkast",
						"data" => "cabinet.name",
						"width" => 100
					],
					[
						"title" => "Naam",
						"data" => "name",
						"width" => 150
					],
					[
						"title" => "Merk",
						"data" => "brand",
						"width" => 150
					],
					[
						"title" => "Type",
						"data" => "type",
						"width" => 100
					],
					[
						"title" => "Beschrijving",
						"data" => "description"
					],
					[
						"title" => "Serienummer",
						"data" => "serialnumber",
						"width" => 150
					],
					[
						"title" => "MAC Adres",
						"data" => "macaddress",
						"width" => 150
					],
					[
						"title" => "# Poorten",
						"data" => "ports",
						"width" => 100
					],
					[
						"type" => "url",
						"title" => "Beheerlink",
						"data" => "ip",
						"width" => 100
					]
				]
			);
			$rows = (new ManagementSwitch)->get();
			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementSwitch)->get($id)));
		$this->handle();
	}

	public function getAccessPoint($view, $id = null)
	{
		if ($view == "select") {
			$schoolId = Helpers::input()->get("parentValue");

			$this->appendToJson("items", (new ManagementAccesspoint)->getBySchool($schoolId));
		} else if ($view == "table") {

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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Gebouw",
						"data" => "building.name",
						"width" => 100
					],
					[
						"title" => "Lokaal",
						"data" => "room.fullNumber",
						"width" => 100
					],
					[
						"title" => "Naam",
						"data" => "name"
					],
					[
						"title" => "Merk",
						"data" => "brand",
						"width" => 150
					],
					[
						"title" => "Model",
						"data" => "model",
						"width" => 100
					],
					[
						"title" => "Firmware",
						"data" => "firmware",
						"width" => 100
					],
					[
						"title" => "Serienummer",
						"data" => "serialnumber",
						"width" => 150
					],
					[
						"title" => "MAC Adres",
						"data" => "macaddress",
						"width" => 150
					],
					[
						"type" => "url",
						"title" => "Beheerlink",
						"data" => "ip",
						"width" => 150
					]
				]
			);
			$rows = (new ManagementAccesspoint)->get();
			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementAccesspoint)->get($id)));
		$this->handle();
	}

	public function getComputer($view, $id = null)
	{
		if ($view == "select") {
			$schoolId = Helpers::input()->get("schoolId");
			$type = Helpers::input()->get("type");

			$this->appendToJson("items", (new ManagementComputer)->getBySchoolAndType($schoolId, $type));
		} else if ($view == "table") {

			$this->appendToJson(
				'columns',
				[
					[
						"type" => "checkbox",
						"class" => ["w-1"],
						"data" => "id"
					],
					[
						"type" => "icon",
						"class" => ["w-1"],
						"title" => "Type",
						"data" => "typeIcon"
					],
					[
						"type" => "badge",
						"title" => "School",
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Gebouw",
						"data" => "building.name",
						"width" => 100
					],
					[
						"title" => "Lokaal",
						"data" => "room.fullNumber",
						"width" => 100
					],
					[
						"title" => "Naam",
						"data" => "name",
						"width" => 200
					],
					[
						"title" => "Laptopkar",
						"data" => "cart.name",
						"width" => 100
					],
					[
						"title" => "Merk",
						"data" => "systemManufacturer"
					],
					[
						"title" => "Model",
						"data" => "systemModel"
					],
					[
						"title" => "OS Informatie",
						"data" => "osInformation",
						"width" => 300
					],
					[
						"title" => "Serienummer",
						"data" => "systemSerialnumber",
						"width" => 200
					]
				]
			);
			$rows = (new ManagementComputer)->get();
			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementComputer)->get($id)));
		$this->handle();
	}

	public function getIpad($view, $id = null)
	{
		if ($view == "select") {
			$schoolId = Helpers::input()->get("parentValue", Helpers::input()->get("schoolId"));

			$this->appendToJson("items", (new ManagementIpad)->getBySchool($schoolId));
		} else if ($view == "table") {

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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Devicenaam",
						"data" => "deviceName",
						"width" => 150
					],
					[
						"title" => "Ipadkar",
						"data" => "cart.name",
						"width" => 100
					],
					[
						"title" => "Serienummer",
						"data" => "serialNumber",
						"width" => 150
					],
					[
						"title" => "Modelnaam",
						"data" => "modelName",
						"width" => 200
					],
					[
						"title" => "OS",
						"data" => "osDescription",
						"width" => 100
					],
					[
						"type" => "badge",
						"title" => "Batterij",
						"data" => "batteryLevelPercentage",
						"backgroundColorCustom" => "batteryLevelColor",
						"width" => 50
					],
					[
						"type" => "badge",
						"title" => "Capaciteit (%)",
						"data" => "availablePercentageLabel",
						"backgroundColorCustom" => "availablePercentageColor",
						"width" => 50
					],
					[
						"title" => "Capaciteit",
						"data" => "capacityFormatted",
						"width" => 200
					],
					[
						"title" => "UdId",
						"data" => "udId"
					],
				]
			);
			$rows = (new ManagementIpad)->get();
			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementIpad)->get($id)));
		$this->handle();
	}

	public function getBeamer($view, $id = null)
	{
		if ($view == "select") {
			$schoolId = Helpers::input()->get("parentValue", Helpers::input()->get("schoolId"));

			$this->appendToJson("items", (new ManagementBeamer)->getBySchool($schoolId));
		} else if ($view == "table") {

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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Gebouw",
						"data" => "building.name",
						"width" => 100
					],
					[
						"title" => "Lokaal",
						"data" => "room.fullNumber",
						"width" => 100
					],
					[
						"title" => "Merk",
						"data" => "brand",
						"width" => 200
					],
					[
						"title" => "Type",
						"data" => "type",
						"width" => 200
					],
					[
						"title" => "Serienummer",
						"data" => "serialnumber"
					]
				]
			);
			$rows = (new ManagementBeamer)->get();
			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementBeamer)->get($id)));
		$this->handle();
	}

	public function getPrinter($view, $id = null)
	{
		if ($view == "select") {
			$schoolId = Helpers::input()->get("parentValue", Helpers::input()->get("schoolId"));

			$this->appendToJson("items", (new ManagementPrinter)->getBySchool($schoolId));
		} else if ($view == "table") {

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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Gebouw",
						"data" => "building.name",
						"width" => 100
					],
					[
						"title" => "Lokaal",
						"data" => "room.fullNumber",
						"width" => 100
					],
					[
						"title" => "Naam",
						"data" => "name",
						"width" => 250
					],
					[
						"title" => "Merk",
						"data" => "brand",
						"width" => 200
					],
					[
						"title" => "Type",
						"data" => "type",
						"width" => 200
					],
					[
						"title" => "Kleurmodus",
						"data" => "colormodeFull",
						"width" => 100
					],
					[
						"title" => "Serienummer",
						"data" => "serialnumber"
					]
				]
			);
			$rows = (new ManagementPrinter)->get();
			Arrays::each($rows, fn ($r) => $r->link()->init());
			$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementPrinter)->get($id)));
		$this->handle();
	}

	public function getCart($view, $id = null)
	{
		if ($view == "select") {
			$schoolId = Helpers::input()->get("parentValue", Helpers::input()->get("schoolId"));
			$type = Helpers::input()->get("type");

			$rows = (new ManagementCart)->getBySchoolAndType($schoolId, $type);
			Arrays::each($rows, fn ($r) => $r->link());
			//filter for empty carts => impossible to add a new device to an empty cart
			//$rows = Arrays::filter($rows, fn ($r) => !empty($r->devices));
			$this->appendToJson("items", $rows);
		} else if ($view == "table") {
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
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"type" => "icon",
						"class" => ["w-1"],
						"title" => "Type",
						"data" => "typeIcon"
					],
					[
						"title" => "Naam",
						"data" => "name"
					]
				]
			);
			$rows = (new ManagementCart)->get();
			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", $rows);
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new ManagementCart)->get($id)));
		$this->handle();
	}

	// POST
	public function postBuilding($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementBuildingRepo = new ManagementBuilding;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($name) || Input::empty($name)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$building = is_null($id) ? new ObjectManagementBuilding() : $managementBuildingRepo->get($id)[0];

				if (!empty($managementBuildingRepo->checkAlreadyExist($schoolId, $name, $id))) {
					$this->setValidation("name", "Er bestaat al een gebouw met deze naam!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "A building with this name already exists");
				} else {
					$building->schoolId = $schoolId;
					$building->name = $name;
					$newBuilding = $managementBuildingRepo->set($building);

					Log::write(description: "Added/Edited building {$name} with id " . (is_null($id) ? $newBuilding : $id));
					$this->setToast("Beheer - Gebouw", "Het gebouw {$name} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$building = Arrays::firstOrNull($managementBuildingRepo->get($_id));

				if (!is_null($building)) {
					$building->deleted = 1;
					$managementBuildingRepo->set($building);

					Log::write(description: "Deleted building {$building->name} with id {$building->id}");
					$this->setToast("Beheer - Gebouw", "Het gebouw {$building->name} is verwijderd!");
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function postRooms($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$floor = Helpers::input()->post('floor')?->getValue();
		$number = Helpers::input()->post('number')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementRoomsRepo = new ManagementRoom;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) {
				$this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Building is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$room = is_null($id) ? new ObjectManagementRoom() : $managementRoomsRepo->get($id)[0];

				if (!empty($managementRoomsRepo->checkAlreadyExist($schoolId, $buildingId, $floor, $number, $id))) {
					$this->setValidation("floor", "Er bestaat al een lokaal met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "There is already a room with this combination");
				} else {
					$room->schoolId = $schoolId;
					$room->buildingId = $buildingId;
					$room->floor = $floor;
					$room->number = $number;
					$newroom = $managementRoomsRepo->set($room);

					Log::write(description: "Added/Edited room {$number} with id " . (is_null($id) ? $newroom : $id));
					$this->setToast("Beheer - Lokaal", "Het lokaal {$room->floor}." . sprintf("%02d", $room->number) . " is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$room = Arrays::firstOrNull($managementRoomsRepo->get($_id));

				if (!is_null($room)) {
					$room->deleted = 1;
					$managementRoomsRepo->set($room);

					Log::write(description: "Deleted room {$room->name} with id {$room->id}");
					$this->setToast("Beheer - Lokaal", "Het lokaal {$room->floor}." . sprintf("%02d", $room->number) . " is verwijderd!");
				}
			}
		}
		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function postCabinet($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementCabinetRepo = new ManagementCabinet;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) {
				$this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Building is not filled in");
			}
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) {
				$this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Room is not filled in");
			}
			if (!Input::check($name) || Input::empty($roomId)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$cabinet = is_null($id) ? new ObjectManagementCabinet() : $managementCabinetRepo->get($id)[0];

				if (!empty($managementCabinetRepo->checkAlreadyExist($schoolId, $buildingId, $roomId, $name, $id))) {
					$this->setValidation("name", "Er bestaat al een netwerkkast met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "There is already a cabinet with this combination");
				} else {
					$cabinet->schoolId = $schoolId;
					$cabinet->buildingId = $buildingId;
					$cabinet->roomId = $roomId;
					$cabinet->name = $name;
					$newcabinet = $managementCabinetRepo->set($cabinet);

					Log::write(description: "Added/Edited cabinet {$name} with id " . (is_null($id) ? $newcabinet : $id));
					$this->setToast("Beheer - Netwerkkast", "De netwerkkast {$name} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$cabinet = Arrays::firstOrNull($managementCabinetRepo->get($_id));

				if (!is_null($cabinet)) {
					$cabinet->deleted = 1;
					$managementCabinetRepo->set($cabinet);

					Log::write(description: "Deleted cabinet {$cabinet->name} with id {$cabinet->id}");
					$this->setToast("Beheer - Netwerkkast", "De netwerkkast {$cabinet->name} is verwijderd!");
				}
			}
		}
		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function postPatchpanel($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$cabinetId = Helpers::input()->post('cabinetId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$ports = Helpers::input()->post('ports')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementPatchpanelRepo = new ManagementPatchpanel;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) {
				$this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Building is not filled in");
			}
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) {
				$this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Room is not filled in");
			}
			if (!Input::check($cabinetId, Input::INPUT_TYPE_INT) || Input::empty($cabinetId)) {
				$this->setValidation("cabinetId", "Netwerkkast moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Cabinet is not filled in");
			}
			if (!Input::check($name) || Input::empty($roomId)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}
			if (!Input::check($ports, Input::INPUT_TYPE_INT) || Input::empty($ports)) {
				$this->setValidation("ports", "Aantal patchpunten moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Number of patch points is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$patchpanel = is_null($id) ? new ObjectManagementPatchpanel() : $managementPatchpanelRepo->get($id)[0];

				if (!empty($managementPatchpanelRepo->checkAlreadyExist($schoolId, $buildingId, $roomId, $cabinetId, $name, $id))) {
					$this->setValidation("name", "Er bestaat al een patchpaneel met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "There is already patchpanel with this combination");
				} else {
					$patchpanel->schoolId = $schoolId;
					$patchpanel->buildingId = $buildingId;
					$patchpanel->roomId = $roomId;
					$patchpanel->cabinetId = $cabinetId;
					$patchpanel->name = $name;
					$patchpanel->ports = $ports;
					$newpatchpanel = $managementPatchpanelRepo->set($patchpanel);

					Log::write(description: "Added/Edited patchpanel {$name} with id " . (is_null($id) ? $newpatchpanel : $id));
					$this->setToast("Beheer - Patchpanel", "Het patchpanel {$name} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$patchpanel = Arrays::firstOrNull($managementPatchpanelRepo->get($_id));

				if (!is_null($patchpanel)) {
					$patchpanel->deleted = 1;
					$managementPatchpanelRepo->set($patchpanel);

					Log::write(description: "Deleted patchpanel {$patchpanel->name} with id {$patchpanel->id}");
					$this->setToast("Beheer - Patchpanel", "Het patchpanel {$patchpanel->name} is verwijderd!");
				}
			}
		}
		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function postFirewall($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$cabinetId = Helpers::input()->post('cabinetId')?->getValue();
		$hostname = Helpers::input()->post('hostname')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$model = Helpers::input()->post('model')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$macaddress = Helpers::input()->post('macaddress')?->getValue();
		$firmware = Helpers::input()->post('firmware')?->getValue();
		$interface = Helpers::input()->post('interface')?->getValue();
		$username = Helpers::input()->post('username')?->getValue();
		$password = Helpers::input()->post('password')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementFirewallRepo = new ManagementFirewall;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) {
				$this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Building is not filled in");
			}
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) {
				$this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Room is not filled in");
			}
			if (!Input::check($cabinetId, Input::INPUT_TYPE_INT) || Input::empty($cabinetId)) {
				$this->setValidation("cabinetId", "Netwerkkast moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Cabinet is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$firewall = is_null($id) ? new ObjectManagementFirewall() : $managementFirewallRepo->get($id)[0];

				if (!empty($managementFirewallRepo->checkAlreadyExist($schoolId, $buildingId, $roomId, $cabinetId, $hostname, $id))) {
					$this->setValidation("hostname", "Er bestaat al een firewall met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "There is already a firewall with this combination");
				} else {
					$firewall->schoolId = $schoolId;
					$firewall->buildingId = $buildingId;
					$firewall->roomId = $roomId;
					$firewall->cabinetId = $cabinetId;
					$firewall->hostname = $hostname;
					$firewall->brand = $brand;
					$firewall->model = $model;
					$firewall->serialnumber = $serialnumber;
					$firewall->firmware = $firmware;
					$firewall->macaddress = $macaddress;
					$firewall->interface = rtrim($interface, ":");
					$firewall->username = $username;
					$firewall->password = $password;
					$newfirewall = $managementFirewallRepo->set($firewall);

					Log::write(description: "Added/Edited firewall {$hostname} with id " . (is_null($id) ? $newfirewall : $id));
					$this->setToast("Beheer - Firewall", "De firewall {$hostname} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$firewall = Arrays::firstOrNull($managementFirewallRepo->get($_id));

				if (!is_null($firewall)) {
					$firewall->deleted = 1;
					$managementFirewallRepo->set($firewall);

					Log::write(description: "Deleted firewall {$firewall->name} with id {$firewall->id}");
					$this->setToast("Beheer - Firewall", "De firewall {$firewall->hostname} is verwijderd!");
				}
			}
		}
		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function postSwitch($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$cabinetId = Helpers::input()->post('cabinetId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$type = Helpers::input()->post('type')?->getValue();
		$description = Helpers::input()->post('description')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$macaddress = Helpers::input()->post('macaddress')?->getValue();
		$ip = Helpers::input()->post('ip')?->getValue();
		$username = Helpers::input()->post('username')?->getValue();
		$password = Helpers::input()->post('password')?->getValue();
		$ports = Helpers::input()->post('ports')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementSwitchRepo = new ManagementSwitch;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) {
				$this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Building is not filled in");
			}
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) {
				$this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Room is not filled in");
			}
			if (!Input::check($ports, Input::INPUT_TYPE_INT) || Input::empty($ports)) {
				$this->setValidation("ports", "Aantal poorten moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Number of ports is not filled in");
			}
			if (!Input::check($name) || Input::empty($name)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}
			if (!Input::check($brand) || Input::empty($brand)) {
				$this->setValidation("brand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Brand is not filled in");
			}
			if (!Input::check($type) || Input::empty($type)) {
				$this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Type is not filled in");
			}
			if (!Input::check($serialnumber) || Input::empty($serialnumber)) {
				$this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Serialnumber is not filled in");
			}
			if (!Input::check($macaddress) || Input::empty($macaddress)) {
				$this->setValidation("macaddress", "MAC adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Mac address is not filled in");
			}
			if (!Input::check($ip) || Input::empty($ip)) {
				$this->setValidation("ip", "IP adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "IP address is not filled in");
			}
			if (!Input::check($password) || Input::empty($password)) {
				$this->setValidation("password", "Wachtwoord moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Password is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$switch = is_null($id) ? new ObjectManagementSwitch : $managementSwitchRepo->get($id)[0];

				if (!empty($managementSwitchRepo->checkAlreadyExist($schoolId, $name, $id))) {
					$this->setValidation("name", "Er bestaat al een switch met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "There is already a switch with this combination");
				} else {
					$switch->schoolId = $schoolId;
					$switch->buildingId = $buildingId;
					$switch->roomId = $roomId;
					$switch->cabinetId = $cabinetId;
					$switch->name = $name;
					$switch->brand = $brand;
					$switch->type = $type;
					$switch->description = $description;
					$switch->serialnumber = $serialnumber;
					$switch->macaddress = $macaddress;
					$switch->ip = rtrim($ip, ":");
					$switch->username = $username;
					$switch->password = $password;
					$switch->ports = $ports;
					$newswitch = $managementSwitchRepo->set($switch);

					Log::write(description: "Added/Edited switch {$name} with id " . (is_null($id) ? $newswitch : $id));
					$this->setToast("Beheer - Switch", "De switch {$name} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$switch = Arrays::firstOrNull($managementSwitchRepo->get($_id));

				if (!is_null($switch)) {
					$switch->deleted = 1;
					$managementSwitchRepo->set($switch);

					Log::write(description: "Deleted switch {$switch->name} with id {$switch->id}");
					$this->setToast("Beheer - Switch", "De switch {$switch->name} is verwijderd!");
				}
			}
		}
		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function postAccessPoint($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$model = Helpers::input()->post('model')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$macaddress = Helpers::input()->post('macaddress')?->getValue();
		$firmware = Helpers::input()->post('firmware')?->getValue();
		$ip = Helpers::input()->post('ip')?->getValue();
		$username = Helpers::input()->post('username')?->getValue();
		$password = Helpers::input()->post('password')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementAccesspointRepo = new ManagementAccesspoint;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) {
				$this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Building is not filled in");
			}
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) {
				$this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Room is not filled in");
			}
			if (!Input::check($name) || Input::empty($name)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}
			if (!Input::check($brand) || Input::empty($brand)) {
				$this->setValidation("brand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Brand is not filled in");
			}
			if (!Input::check($model) || Input::empty($model)) {
				$this->setValidation("model", "Model moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Model is not filled in");
			}
			if (!Input::check($serialnumber) || Input::empty($serialnumber)) {
				$this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Serialnumber is not filled in");
			}
			if (!Input::check($macaddress) || Input::empty($macaddress) || Strings::equal($macaddress, "__:__:__:__:__:__")) {
				$this->setValidation("macaddress", "MAC adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Mac address is not filled in");
			}
			if (!Input::check($ip) || Input::empty($ip) || Strings::equal($ip, "_._._._:")) {
				$this->setValidation("ip", "IP adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Ip address is not filled in");
			}
			if (!Input::check($password) || Input::empty($password)) {
				$this->setValidation("password", "Wachtwoord moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Password is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$accesspoint = is_null($id) ? new ObjectManagementAccesspoint() : $managementAccesspointRepo->get($id)[0];

				if (!empty($managementAccesspointRepo->checkAlreadyExist($schoolId, $buildingId, $roomId, $name, $id))) {
					$this->setValidation("name", "Er bestaat al een accesspoint met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "The is already an access point with this combination");
				} else {
					$accesspoint->schoolId = $schoolId;
					$accesspoint->buildingId = $buildingId;
					$accesspoint->roomId = $roomId;
					$accesspoint->name = $name;
					$accesspoint->brand = $brand;
					$accesspoint->model = $model;
					$accesspoint->serialnumber = $serialnumber;
					$accesspoint->macaddress = $macaddress;
					$accesspoint->firmware = $firmware;
					$accesspoint->ip = rtrim($ip, ":");
					$accesspoint->username = $username;
					$accesspoint->password = $password;
					$newaccesspoint = $managementAccesspointRepo->set($accesspoint);

					Log::write(description: "Added/Edited access point {$name} with id " . (is_null($id) ? $newaccesspoint : $id));
					$this->setToast("Beheer - Access Point", "De access point {$name} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$accesspoint = Arrays::firstOrNull($managementAccesspointRepo->get($_id));

				if (!is_null($accesspoint)) {
					$accesspoint->deleted = 1;
					$managementAccesspointRepo->set($accesspoint);

					Log::write(description: "Deleted access point {$accesspoint->name} with id {$accesspoint->id}");
					$this->setToast("Beheer - Access Point", "De access point {$accesspoint->name} is verwijderd!");
				}
			}
		}
		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function postComputer($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$cartId = Helpers::input()->post('cartId')?->getValue();
		$type = Helpers::input()->post('type')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$osType = Helpers::input()->post('osType')?->getValue();
		$osNumber = Helpers::input()->post('osNumber')?->getValue();
		$osBuild = Helpers::input()->post('osBuild')?->getValue();
		$osArchitecture = Helpers::input()->post('osArchitecture')?->getValue();
		$systemManufacturer = Helpers::input()->post('systemManufacturer')?->getValue();
		$systemModel = Helpers::input()->post('systemModel')?->getValue();
		$systemMemory = Helpers::input()->post('systemMemory')?->getValue();
		$systemProcessor = Helpers::input()->post('systemProcessor')?->getValue();
		$systemSerialnumber = Helpers::input()->post('systemSerialnumber')?->getValue();
		$systemBiosManufacturer = Helpers::input()->post('systemBiosManufacturer')?->getValue();
		$systemBiosVersion = Helpers::input()->post('systemBiosVersion')?->getValue();
		$systemDrive = Helpers::input()->post('systemDrive')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementComputerRepo = new ManagementComputer;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($type) || Input::empty($type)) {
				$this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Type is not filled in");
			}
			if (!Input::check($name) || Input::empty($name)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}
			if (!Input::check($systemManufacturer) || Input::empty($systemManufacturer)) {
				$this->setValidation("systemManufacturer", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "System manufacturer");
			}
			if (!Input::check($systemModel) || Input::empty($systemModel)) {
				$this->setValidation("systemModel", "Model moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "System model is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$computer = is_null($id) ? new ObjectManagementComputer : $managementComputerRepo->get($id)[0];

				if (!empty($managementComputerRepo->checkAlreadyExist($schoolId, $buildingId, $roomId, $name, $id))) {
					$this->setValidation("name", "Er bestaat al een computer met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "There is already a computer with this combination");
				} else {
					$computer->schoolId = $schoolId;
					$computer->buildingId = $buildingId;
					$computer->roomId = $roomId;
					$computer->cartId = $cartId;
					$computer->type = $type;
					$computer->name = $name;
					$computer->osType = $osType;
					$computer->osNumber = $osNumber;
					$computer->osBuild = $osBuild;
					$computer->osArchitecture = $osArchitecture;
					$computer->systemManufacturer = $systemManufacturer;
					$computer->systemModel = $systemModel;
					$computer->systemMemory = $systemMemory;
					$computer->systemProcessor = $systemProcessor;
					$computer->systemSerialnumber = $systemSerialnumber;
					$computer->systemBiosManufacturer = $systemBiosManufacturer;
					$computer->systemBiosVersion = $systemBiosVersion;
					$computer->systemDrive = $systemDrive;
					$newcomputer = $managementComputerRepo->set($computer);

					Log::write(description: "Added/Edited computer {$name} with id " . (is_null($id) ? $newcomputer : $id));
					$this->setToast("Beheer - Computer", "De computer {$name} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$computer = Arrays::firstOrNull($managementComputerRepo->get($_id));

				if (!is_null($computer)) {
					$computer->deleted = 1;
					$managementComputerRepo->set($computer);

					Log::write(description: "Deleted computer {$computer->name} with id {$computer->id}");
					$this->setToast("Beheer - Computer", "De computer {$computer->name} is verwijderd!");
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function postIpad($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$devicename = Helpers::input()->post('deviceName')?->getValue();
		$cartId = Helpers::input()->post('cartId')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementIpadRepo = new ManagementIpad;

		if ($faction !== "delete") {
			if ($this->validationIsAllGood()) {
				$ipad = is_null($id) ? new ObjectManagementIpad() : $managementIpadRepo->get($id)[0];

				if (!empty($managementIpadRepo->checkAlreadyExist($schoolId, $devicename, $id))) {
					$this->setValidation("cartId", "Er bestaat al een ipad met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "There is already an ipad with this combination");
				} else {
					$ipad->cartId = $cartId;
					$newipad = $managementIpadRepo->set($ipad);

					Log::write(description: "Edited ipad {$devicename} with id " . (is_null($id) ? $newipad : $id));
					$this->setToast("Beheer - Ipad", "De ipad {$devicename} is opgeslagen!");
				}
			}
		}
		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function PostBeamer($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$type = Helpers::input()->post('type')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementBeamerRepo = new ManagementBeamer;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) {
				$this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Building is not filled in");
			}
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) {
				$this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Room is not filled in");
			}
			if (!Input::check($brand) || Input::empty($brand)) {
				$this->setValidation("brand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Brand is not filled in");
			}
			if (!Input::check($type) || Input::empty($type)) {
				$this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Type is not filled in");
			}
			if (!Input::check($serialnumber) || Input::empty($serialnumber)) {
				$this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Serial number is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$beamer = is_null($id) ? new ObjectManagementBeamer() : $managementBeamerRepo->get($id)[0];

				if (!empty($managementBeamerRepo->checkAlreadyExist($schoolId, $serialnumber, $id))) {
					$this->setValidation("brand", "Er bestaat al een beamer met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: Log::TYPE_ERROR, description: "There is already a beamer with this combination");
				} else {
					$beamer->schoolId = $schoolId;
					$beamer->buildingId = $buildingId;
					$beamer->roomId = $roomId;
					$beamer->brand = $brand;
					$beamer->type = $type;
					$beamer->serialnumber = $serialnumber;
					$newbeamer = $managementBeamerRepo->set($beamer);

					Log::write(description: "Added/Edited beamer {$brand} {$type} with id " . (is_null($id) ? $newbeamer : $id));
					$this->setToast("Beheer - Beamer", "De beamer {$brand} {$type} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$beamer = Arrays::firstOrNull($managementBeamerRepo->get($_id));

				if (!is_null($beamer)) {
					$beamer->deleted = 1;
					$managementBeamerRepo->set($beamer);

					Log::write(description: "Deleted beamer {$beamer->brand} {$beamer->type} with id {$beamer->id}");
					$this->setToast("Beheer - Beamer", "De beamer {$beamer->brand} {$beamer->type} is verwijderd!");
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function PostPrinter($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$buildingId = Helpers::input()->post('buildingId')?->getValue();
		$roomId = Helpers::input()->post('roomId')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$brand = Helpers::input()->post('brand')?->getValue();
		$type = Helpers::input()->post('type')?->getValue();
		$serialnumber = Helpers::input()->post('serialnumber')?->getValue();
		$colormode = Helpers::input()->post('colormode')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementPrinterRepo = new ManagementPrinter;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($buildingId, Input::INPUT_TYPE_INT) || Input::empty($buildingId)) {
				$this->setValidation("buildingId", "Gebouw moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Building is not filled in");
			}
			if (!Input::check($roomId, Input::INPUT_TYPE_INT) || Input::empty($roomId)) {
				$this->setValidation("roomId", "Lokaal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Room is not filled in");
			}
			if (!Input::check($name) || Input::empty($name)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}
			if (!Input::check($brand) || Input::empty($brand)) {
				$this->setValidation("brand", "Merk moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Brand is not filled in");
			}
			if (!Input::check($type) || Input::empty($type)) {
				$this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Type is not filled in");
			}
			if (!Input::check($serialnumber) || Input::empty($serialnumber)) {
				$this->setValidation("serialnumber", "Serienummer moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Serial number is not filled in");
			}
			if (!Input::check($colormode) || Input::empty($colormode)) {
				$this->setValidation("colormode", "Kleurmodus moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Colormode is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$printer = is_null($id) ? new ObjectManagementPrinter() : $managementPrinterRepo->get($id)[0];

				if (!empty($managementPrinterRepo->checkAlreadyExist($schoolId, $serialnumber, $id))) {
					$this->setValidation("brand", "Er bestaat al een printer met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: "E", description: "There is already a printer with this combination");
				} else {
					$printer->schoolId = $schoolId;
					$printer->buildingId = $buildingId;
					$printer->roomId = $roomId;
					$printer->name = $name;
					$printer->brand = $brand;
					$printer->type = $type;
					$printer->serialnumber = $serialnumber;
					$printer->colormode = $colormode;
					$newprinter = $managementPrinterRepo->set($printer);

					Log::write(description: "Added/Edited printer {$name} with id " . (is_null($id) ? $newprinter : $id));
					$this->setToast("Beheer - Printer", "De printer {$name} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$printer = Arrays::firstOrNull($managementPrinterRepo->get($_id));

				if (!is_null($printer)) {
					$printer->deleted = 1;
					$managementPrinterRepo->set($printer);

					Log::write(description: "Deleted printer {$printer->name} with id {$printer->id}");
					$this->setToast("Beheer - Printer", "De printer {$printer->name} is verwijderd!");
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}

	public function postCart($view, $id = null)
	{
		$schoolId = Helpers::input()->post('schoolId')?->getValue();
		$type = Helpers::input()->post('type')?->getValue();
		$name = Helpers::input()->post('name')?->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$managementCartRepo = new ManagementCart;

		if ($faction !== "delete") {
			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}
			if (!Input::check($type) || Input::empty($type)) {
				$this->setValidation("type", "Type moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Type is not filled in");
			}
			if (!Input::check($name) || Input::empty($name)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$cart = is_null($id) ? new ObjectManagementCart() : $managementCartRepo->get($id)[0];

				if (!empty($managementCartRepo->checkAlreadyExist($schoolId, $type, $name, $id))) {
					$this->setValidation("name", "Er bestaat al een kar met deze combinatie!", self::VALIDATION_STATE_INVALID);
					Log::write(type: "E", description: "There is already a cart with this combination");
				} else {
					$cart->schoolId = $schoolId;
					$cart->type = $type;
					$cart->name = $name;
					$newcart = $managementCartRepo->set($cart);

					Log::write(description: "Added/Edited cart {$name} with id " . (is_null($id) ? $newcart : $id));
					$this->setToast("Beheer - Kar", "De kar {$name} is opgeslagen!");
				}
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$cart = Arrays::firstOrNull($managementCartRepo->get($_id));

				if (!is_null($cart)) {
					$cart->deleted = 1;
					$managementCartRepo->set($cart);

					Log::write(description: "Deleted cart {$cart->name} with id {$cart->id}");
					$this->setToast("Beheer - Kar", "De kar {$cart->name} is verwijderd!");
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
		}
		$this->handle();
	}
}
