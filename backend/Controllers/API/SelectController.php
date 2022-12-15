<?php

namespace Controllers\API;

use Security\User;
use Controllers\ApiController;
use Database\Repository\School;
use Database\Repository\UserAddress;

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

	public function userAddress()
	{
		$this->appendToJson("items", (new UserAddress)->getByUserId(User::getLoggedInUser()->id));
		$this->handle();
	}
}
