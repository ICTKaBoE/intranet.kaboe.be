<?php

namespace Controllers\API;

use Security\User;
use Controllers\ApiController;
use Database\Repository\CheckStudentRelationInsz;
use Database\Repository\LocalUser;
use Database\Repository\Module;
use Database\Repository\School;
use Database\Repository\UserAddress;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;

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

	public function modulesAssignRights()
	{
		$this->appendToJson("items", (new Module)->get());
		$this->handle();
	}

	public function settingsRightsUsers()
	{
		$this->appendToJson("items", Arrays::orderBy(Arrays::filter((new LocalUser)->get(), fn ($lu) => Strings::isNotBlank($lu->fullName)), "fullName"));
		$this->handle();
	}
}
