<?php

namespace Database\Object;

use Database\Repository\School;
use Database\Interface\CustomObject;

class ManagementBuilding extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"name",
		"deleted"
	];

	public function init()
	{
		$this->_orderfield = "{$this->schoolId}-{$this->name}";
	}

	public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
		return $this;
	}
}
