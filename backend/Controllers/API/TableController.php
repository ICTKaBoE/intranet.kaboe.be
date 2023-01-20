<?php

namespace Controllers\API;

use Helpers\Icon;
use Security\User;
use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\UserHomeWorkDistance;
use Database\Repository\CheckStudentRelationInsz;
use Database\Repository\UserSecurity;

class TableController extends ApiController
{
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
			if (!is_null($school)) $studentRelationInsz = Arrays::filter($studentRelationInsz, fn ($s) => Strings::equal($s->school, $school->getValue()));
			if (!is_null($class) && Strings::isNotBlank($class->getValue()) && !(Strings::equal($class->getValue(), 0) || Strings::equal($class->getValue(), SELECT_ALL_VALUES))) $studentRelationInsz = Arrays::filter($studentRelationInsz, fn ($s) => Strings::equal($s->class, $class->getValue()));

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
}
