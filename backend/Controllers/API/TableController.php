<?php

namespace Controllers\API;

use Helpers\Icon;
use Security\User;
use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\School;
use Database\Repository\Helpdesk;
use Database\Repository\UserSecurity;
use Database\Repository\ManagementRoom;
use Database\Repository\ManagementVlan;
use Database\Repository\NoteScreenPage;
use Database\Repository\ManagementCabinet;
use Database\Repository\NoteScreenArticle;
use Database\Repository\ManagementBuilding;
use Database\Repository\ManagementFirewall;
use Database\Repository\ManagementPatchpanel;
use Database\Repository\UserHomeWorkDistance;
use Database\Repository\CheckStudentRelationInsz;
use Database\Repository\ManagementAccesspoint;
use Database\Repository\ManagementComputer;
use Database\Repository\ManagementSwitch;
use Database\Repository\UserAddress;

class TableController extends ApiController
{
	public function userAddress()
	{
		$this->appendToJson(
			key: 'columns',
			data: [
				[
					"type" => "icon",
					"title" => "Huidig",
					"data" => "currentIcon",
					"class" => ["w-1"]
				],
				[
					"title" => "Straat",
					"data" => "street",
				],
				[
					"title" => "Huisnummer",
					"data" => "number",
					"width" => 100
				],
				[
					"title" => "Bus",
					"data" => "bus",
					"width" => 100
				],
				[
					"title" => "Postcode",
					"data" => "zipcode",
					"width" => 100
				],
				[
					"title" => "Stad/Gemeente",
					"data" => "city",
					"width" => 300
				],
				[
					"title" => "Land",
					"data" => "country",
					"width" => 200
				]
			]
		);

		$distances = (new UserAddress)->getByUserId(User::getLoggedInUser()->id);
		$this->appendToJson("rows", array_values($distances));

		$this->handle();
	}

	public function distances()
	{
		$this->appendToJson(
			key: 'columns',
			data: [
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "Alias",
					"data" => "alias",
					"width" => "10%"
				],
				[
					"title" => "Startadres",
					"data" => "startAddress.formatted",
				],
				[
					"title" => "Eindbestemming",
					"data" => "endSchool.name",
					"width" => "10%"
				],
				[
					"type" => "double",
					"title" => "Afstand",
					"data" => "distance",
					"class" => ["w-1"],
					"format" => [
						"suffix" => " km",
						"precision" => 2
					]
				]
			]
		);

		$distances = (new UserHomeWorkDistance)->getByUserId(User::getLoggedInUser()->id);
		Arrays::each($distances, fn ($d) => $d->link());
		$this->appendToJson("rows", array_values($distances));

		$this->handle();
	}

	public function checkStudentRelationInsz()
	{
		$school = Helpers::input()->get('school');
		$class  = Helpers::input()->get('class');

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
					"title" => Icon::load("thumb-up"),
					"hoverValue" => "generalError",
					"data" => "lockedIcon",
					"class" => ["w-1"],
					"format" => [
						"textColor" => [
							"condition" => [
								"baseOnValue" => "locked",
								"ifTrue" => [
									"check" => true,
									"result" => "green"
								],
								"ifFalse" => [
									"check" => false,
									"result" => "red"
								]
							]
						]
					]
				],
				[
					"type" => "icon",
					"title" => "In Informat?",
					"hoverValue" => "foundInInformatError",
					"data" => "foundInInformatIcon",
					"class" => ["w-1"],
					"format" => [
						"textColor" => [
							"condition" => [
								"baseOnValue" => "foundInInformat",
								"ifTrue" => [
									"check" => true,
									"result" => "green"
								],
								"ifFalse" => [
									"check" => false,
									"result" => "red"
								]
							]
						]
					]
				],
				[
					"title" => "Klas",
					"data" => "classOnly",
				],
				[
					"title" => "Naam kind",
					"data" => "childName",
				],
				[
					"title" => "INSZ kind",
					"data" => "childInsz",
					"width" => "10%",
					"format" => [
						"textColor" => [
							"condition" => [
								"baseOnValue" => "childInszIsCorrect",
								"ifTrue" => [
									"check" => true,
									"result" => "green"
								],
								"ifFalse" => [
									"check" => false,
									"result" => "red"
								]
							]
						]
					]
				],
				[
					"type" => "icon",
					"data" => "childInszIsCorrectIcon",
					"hoverValue" => "childInszIsCorrectError",
					"class" => ["w-1"],
					"format" => [
						"textColor" => [
							"condition" => [
								"baseOnValue" => "childInszIsCorrect",
								"ifTrue" => [
									"check" => true,
									"result" => "green"
								],
								"ifFalse" => [
									"check" => false,
									"result" => "red"
								]
							]
						]
					]
				],
				[
					"title" => "Naam moeder",
					"data" => "motherName",
				],
				[
					"title" => "INSZ moeder",
					"data" => "motherInsz",
					"width" => "10%",
					"format" => [
						"textColor" => [
							"condition" => [
								"baseOnValue" => "motherInszIsCorrect",
								"ifTrue" => [
									"check" => true,
									"result" => "green"
								],
								"ifFalse" => [
									"check" => false,
									"result" => "red"
								]
							]
						]
					]
				],
				[
					"type" => "icon",
					"data" => "motherInszIsCorrectIcon",
					"hoverValue" => "motherInszIsCorrectError",
					"class" => ["w-1"],
					"format" => [
						"textColor" => [
							"condition" => [
								"baseOnValue" => "motherInszIsCorrect",
								"ifTrue" => [
									"check" => true,
									"result" => "green"
								],
								"ifFalse" => [
									"check" => false,
									"result" => "red"
								]
							]
						]
					]
				],
				[
					"title" => "Naam vader",
					"data" => "fatherName",
				],
				[
					"title" => "INSZ vader",
					"data" => "fatherInsz",
					"width" => "10%",
					"format" => [
						"textColor" => [
							"condition" => [
								"baseOnValue" => "fatherInszIsCorrect",
								"ifTrue" => [
									"check" => true,
									"result" => "green"
								],
								"ifFalse" => [
									"check" => false,
									"result" => "red"
								]
							]
						]
					]
				],
				[
					"type" => "icon",
					"data" => "fatherInszIsCorrectIcon",
					"hoverValue" => "fatherInszIsCorrectError",
					"class" => ["w-1"],
					"format" => [
						"textColor" => [
							"condition" => [
								"baseOnValue" => "fatherInszIsCorrect",
								"ifTrue" => [
									"check" => true,
									"result" => "green"
								],
								"ifFalse" => [
									"check" => false,
									"result" => "red"
								]
							]
						]
					]
				]
			]
		);

		if (is_null($school) || is_null($class)) {
			$this->appendToJson("noRowsText", "Gelieve eerst te filteren...");
		} else {
			$studentRelationInsz = (new CheckStudentRelationInsz)->getNotPublished();
			$school = (new School)->get($school->getValue())[0];
			$studentRelationInsz = Arrays::filter($studentRelationInsz, fn ($s) => Strings::equal($s->school, $school->name));
			if (!is_null($class) && Strings::isNotBlank($class->getValue()) && !(Strings::equal($class->getValue(), 0) || Strings::equal($class->getValue(), SELECT_ALL_VALUES))) $studentRelationInsz = Arrays::filter($studentRelationInsz, fn ($s) => Strings::equal($s->class, $class->getValue()));

			$studentRelationInsz = Arrays::orderBy($studentRelationInsz, 'childName');
			Arrays::each($studentRelationInsz, fn ($s) => $s->check());
			$this->appendToJson("rows", array_values($studentRelationInsz));
		}

		$this->handle();
	}

	public function settingsRights()
	{
		$module = Helpers::input()->get('module');

		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "Naam",
					"data" => "user.fullName",
				],
				[
					"title" => "E-mail",
					"data" => "user.username",
				],
				[
					"type" => "icon",
					"title" => Icon::load("eye"),
					"data" => "viewIcon",
					"class" => ["w-1"],
				],
				[
					"type" => "icon",
					"title" => Icon::load("pencil"),
					"data" => "editIcon",
					"class" => ["w-1"],
				],
				[
					"type" => "icon",
					"title" => Icon::load("file-export"),
					"data" => "exportIcon",
					"class" => ["w-1"],
				],
				[
					"type" => "icon",
					"title" => Icon::load("settings"),
					"data" => "changeSettingsIcon",
					"class" => ["w-1"],
				],
				[
					"type" => "icon",
					"title" => Icon::load("lock"),
					"data" => "lockedIcon",
					"class" => ["w-1"],
				],
			]
		);

		if (is_null($module)) {
			$this->appendToJson("noRowsText", "Gelieve eerst een module te kiezen...");
		} else {
			$userRights = (new UserSecurity)->getByModuleId($module->getValue());
			Arrays::each($userRights, fn ($ur) => $ur->link());
			$userRights = Arrays::filter($userRights, fn ($ur) => Strings::isNotBlank($ur->user->fullName));
			$userRights = Arrays::orderBy($userRights, 'tableOrder');
			$this->appendToJson("rows", array_values($userRights));
		}

		$this->handle();
	}

	public function noteScreenPages()
	{
		$schoolId = Helpers::input()->get('schoolId');

		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "Naam",
					"data" => "name",
				],
			]
		);

		$this->appendToJson("rows", array_values((new NoteScreenPage)->getBySchoolId($schoolId)));
		$this->handle();
	}

	public function noteScreenArticles()
	{
		$schoolId = Helpers::input()->get('schoolId');

		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "Pagina",
					"data" => "page.name",
					"width" => 300
				],
				[
					"title" => "Titel",
					"data" => "title",
				],
			]
		);

		$rows = (new NoteScreenArticle)->getBySchoolId($schoolId);
		Arrays::each($rows, fn ($nsa) => $nsa->link());
		$this->appendToJson("rows", array_values($rows));
		$this->handle();
	}

	public function helpdesk($prefix, $type)
	{
		$schoolId = Helpers::url()->getParam("school");
		$creatorId = Helpers::url()->getParam("creator");

		$filters = [
			"schoolId" => $schoolId,
			"creatorId" => $creatorId
		];

		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "#",
					"data" => "number",
					"width" => 150
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
					"width" => 150
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
					"width" => 200
				]
			]
		);

		$rows = (new Helpdesk)->getByViewTypeWithFilters($type, $filters);
		Arrays::each($rows, fn ($r) => $r->link());
		$this->appendToJson("rows", $rows);
		$this->handle();
	}

	public function managementBuilding()
	{
		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "School",
					"data" => "school.name",
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
		$this->handle();
	}

	public function managementRoom()
	{
		$schoolId = Helpers::input()->get('schoolId');

		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "School",
					"data" => "school.name",
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
		$this->handle();
	}

	public function managementCabinet()
	{
		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "School",
					"data" => "school.name",
					"width" => 120
				],
				[
					"title" => "Gebouw",
					"data" => "building.name",
					"width" => 100
				],
				[
					"title" => "Lokaal",
					"data" =>
					"room.fullNumber",
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
		$this->handle();
	}

	public function managementPatchpanel()
	{
		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "School",
					"data" => "school.name",
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
		$this->handle();
	}

	public function managementFirewall()
	{
		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "School",
					"data" => "school.name",
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
				]
			]
		);

		$rows = (new ManagementFirewall)->get();
		Arrays::each($rows, fn ($nsa) => $nsa->link());
		$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		$this->handle();
	}

	public function managementSwitch()
	{
		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "School",
					"data" => "school.name",
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
		Arrays::each($rows, fn ($nsa) => $nsa->link());
		$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		$this->handle();
	}

	public function managementAccesspoint()
	{
		$this->appendToJson(
			'columns',
			[
				[
					"type" => "checkbox",
					"class" => ["w-1"],
					"data" => "id"
				],
				[
					"title" => "School",
					"data" => "school.name",
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
		Arrays::each($rows, fn ($nsa) => $nsa->link());
		$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		$this->handle();
	}

	public function managementComputer()
	{
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
					"title" => "School",
					"data" => "school.name",
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
		Arrays::each($rows, fn ($nsa) => $nsa->link());
		$this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
		$this->handle();
	}
}
