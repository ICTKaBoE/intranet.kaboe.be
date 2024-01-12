<?php

namespace Mapper;

use Ouzo\Utilities\Clock;

class InformatStaffAssignmentDBMapper extends MInterface
{
	protected $mapFields = [
		"informatUID" => "POpdr",
		"informatStaffUID" => "p_persoon",
		"masterNumber" => "Stamnummer",
		"instituteNumber" => "Instelnr",
		"start" => "Begindatum",
		"end" => "Einddatum",
	];

	public function formatFields($obj2)
	{
		$obj2->Begindatum = Clock::at($obj2->Begindatum)->format("Y-m-d");
		$obj2->Einddatum = Clock::at($obj2->Einddatum)->format("Y-m-d");

		return $obj2;
	}
}
