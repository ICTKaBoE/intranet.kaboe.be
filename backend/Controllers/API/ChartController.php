<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\CheckStudentRelationInsz;
use Database\Repository\School;
use Database\Repository\SchoolInstitute;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;

class ChartController extends ApiController
{
	public function checkStudentRelationInsz()
	{
		$repo = new CheckStudentRelationInsz;
		$sRepo = new School;
		$iRepo = new SchoolInstitute;

		$this->appendToJson(["xaxis", "categories"], Arrays::map($sRepo->get(), fn ($s) => $s->name));
		$series = [
			[
				"name" => "Nog goed te keuren",
				"data" => []
			],
			[
				"name" => "Goedgekeurd (niet klaargemaakt)",
				"data" => []
			],
			[
				"name" => "Klaargemaakt",
				"data" => []
			]
		];

		foreach ($sRepo->get() as $idx => $school) {
			$items = $repo->getBySchoolName($school->name);
			Arrays::each($items, fn ($i) => $i->check());

			$toBeChecked = count($items);
			$locked = Arrays::count($items, fn ($i) => Strings::equal($i->locked, true));
			$published = Arrays::count($items, fn ($i) => Strings::equal($i->published, true));

			$toBeChecked -= $locked;
			$locked -= $published;

			$series[0]["data"][$idx] = $toBeChecked;
			$series[1]["data"][$idx] = $locked;
			$series[2]["data"][$idx] = $published;
		}

		$this->appendToJson("series", $series);

		$this->handle();
	}
}
