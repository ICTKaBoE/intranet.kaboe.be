<?php

namespace Mapper;

class InformatStudentDBMapper extends MInterface
{
	protected $mapFields = [
		"informatUID" => "p_persoon",
		"instituteId" => "instelnr",
		"name" => "Naam",
		"firstName" => "Voornaam",
		"insz" => "rijksregnr",
	];
}
