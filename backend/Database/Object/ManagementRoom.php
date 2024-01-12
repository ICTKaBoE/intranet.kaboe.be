<?php

namespace Database\Object;

use Database\Repository\School;
use Database\Interface\CustomObject;
use Database\Repository\ManagementBuilding;

class ManagementRoom extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"buildingId",
		"floor",
		"number",
		"deleted"
	];

	public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
		$this->building = ($this->buildingId == 0 ? false : (new ManagementBuilding)->get($this->buildingId)[0]);
		return $this;
	}

	public function init()
	{
		$this->fullNumber = "{$this->floor}." . sprintf("%02d", $this->number);
		$this->_orderfield = "{$this->schoolId}-{$this->buildingId}-{$this->fullNumber}";
		$this->fullNumberBuilding = "{$this->building->name} - {$this->fullNumber}";
	}
}
