<?php

namespace Database\Object;

use Helpers\Mapping;
use Database\Repository\School;
use Database\Interface\CustomObject;
use Database\Repository\ManagementRoom;
use Database\Repository\ManagementBuilding;

class ManagementPrinter extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"buildingId",
		"roomId",
		"name",
		"brand",
		"type",
		"serialnumber",
		"colormode",
		"deleted"
	];

	public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
		$this->building = ($this->buildingId == 0 ? false : (new ManagementBuilding)->get($this->buildingId)[0]);
		$this->room = ($this->roomId == 0 ? false : (new ManagementRoom)->get($this->roomId)[0]);
		return $this;
	}

	public function init()
	{
		$this->_orderfield = "{$this->schoolId}-{$this->buildingId}-{$this->roomId}-{$this->name}";
		$this->colormodeFull = Mapping::get("management/printer/colormode/{$this->colormode}");

		$this->shortDescription = "{$this->name} ({$this->brand} {$this->type} / SN: {$this->serialnumber})";
	}
}
