<?php

namespace Controllers\API;

use Security\User;
use Controllers\ApiController;
use Database\Repository\UserHomeWorkDistance;
use Ouzo\Utilities\Arrays;

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
}
