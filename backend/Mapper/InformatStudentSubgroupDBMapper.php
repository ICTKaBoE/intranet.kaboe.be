<?php

namespace Mapper;

class InformatStudentSubgroupDBMapper extends MInterface
{
	protected $mapFields = [
		"informatStudentUID" => "p_persoon",
		"class" => "Klascode",
	];
}
