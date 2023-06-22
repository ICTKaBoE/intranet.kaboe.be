<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\School;

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

	public function link()
	{
		$this->school = (new School)->get($this->schoolId)[0];
	}
}
