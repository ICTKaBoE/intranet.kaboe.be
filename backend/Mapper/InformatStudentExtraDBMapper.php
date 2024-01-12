<?php

namespace Mapper;

class InformatStudentExtraDBMapper extends MInterface
{
	protected $mapFields = [
		"informatUID" => "pointer",
		"name" => "naam",
		"firstName" => "voornaam",
		"nickname" => "Tweedevoornaam",
		"masterNumber" => "Rijksregisternummer",
		"bisNumber" => "BISnummer",
	];
}
