<?php

namespace Mapper;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class InformatStudentSubscriptionDBMapper extends MInterface
{
	protected $mapFields = [
		"informatUID" => "PInschrijving",
		"informatStudentUID" => "PPersoon",
		"instituteId" => "Instelnr",
		"status" => "Status",
		"start" => "Begindatum",
		"end" => "Einddatum",
		"grade" => "Graad",
		"year" => "Leerjaar"
	];

	public function formatFields($obj2)
	{
		$obj2->Begindatum = Arrays::first(explode("T", $obj2->Begindatum));
		$obj2->Begindatum = Clock::at($obj2->Begindatum)->format("Y-m-d");

		if (!is_null($obj2->Einddatum)) {
			$obj2->Einddatum = Arrays::first(explode("T", $obj2->Einddatum));
			$obj2->Einddatum = Clock::at($obj2->Einddatum)->format("Y-m-d");
		}

		return $obj2;
	}
}
