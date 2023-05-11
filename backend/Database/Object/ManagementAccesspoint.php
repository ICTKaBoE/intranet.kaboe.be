<?php

namespace Database\Object;

use Database\Repository\School;
use Database\Interface\CustomObject;
use Database\Repository\ManagementRoom;
use Database\Repository\ManagementBuilding;

class ManagementAccesspoint extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"buildingId",
		"roomId",
		"name",
		"brand",
		"model",
		"firmware",
		"serialnumber",
		"macaddress",
		"ip",
		"username",
		"password",
		"deleted"
	];

	public function init()
	{
		$this->_orderfield = "{$this->schoolId}-{$this->name}";
	}

	public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
		$this->building = ($this->buildingId == 0 ? false : (new ManagementBuilding)->get($this->buildingId)[0]);
		$this->room = ($this->roomId == 0 ? false : (new ManagementRoom)->get($this->roomId)[0]);
		return $this;
	}
}
