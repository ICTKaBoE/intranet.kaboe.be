<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class SchoolInstitute extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"instituteNumber",
		"deleted"
	];

	public function init()
	{
		if (strlen($this->instituteNumber) == 5) $this->instituteNumber = "0{$this->instituteNumber}";
	}
}
