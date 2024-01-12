<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\School;

class SchoolClass extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"name",
		"teacher",
		"grade",
		"year",
		"deleted"
	];

	public function init()
	{
		$this->nameWithTeacher = $this->name . ($this->teacher ? " ({$this->teacher})" : "");
	}

	public function link()
	{
		$this->school = (new School)->get($this->schoolId)[0];
	}
}
