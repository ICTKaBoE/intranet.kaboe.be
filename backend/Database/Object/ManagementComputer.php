<?php

namespace Database\Object;

use Database\Repository\School;
use Database\Interface\CustomObject;
use Database\Repository\ManagementRoom;
use Database\Repository\ManagementBuilding;
use Helpers\Icon;
use Helpers\Mapping;
use Ouzo\Utilities\Strings;

class ManagementComputer extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"buildingId",
		"roomId",
		"type",
		"name",
		"osType",
		"osNumber",
		"osBuild",
		"osArchitecture",
		"systemManufacturer",
		"systemModel",
		"systemMemory",
		"systemProcessor",
		"systemSerialnumber",
		"systemBiosManufacturer",
		"systemBiosVersion",
		"systemDrive",
		"deleted"
	];

	public function init()
	{
		$this->_orderfield = "{$this->schoolId}-{$this->name}";
		$this->typeIcon = Icon::load("device-" . (Strings::equal($this->type, "L") ? "laptop" : "desktop"));
		$this->osInformation = Mapping::get("management/computer/osType/{$this->osType}") . " {$this->osNumber} " . Mapping::get("management/computer/osArchitecture/{$this->osArchitecture}") . " ({$this->osBuild})";

		$this->typeFull = Mapping::get("management/computer/type/{$this->type}");
		$this->osTypeFull = Mapping::get("management/computer/osType/{$this->osType}");
		$this->osArchitectureFull = Mapping::get("management/computer/osArchitecture/{$this->osArchitecture}");

		$this->shortDescription = "{$this->name} ({$this->systemManufacturer} {$this->systemModel} / SN: {$this->systemSerialnumber})";
	}

	public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
		$this->building = ($this->buildingId == 0 ? false : (new ManagementBuilding)->get($this->buildingId)[0]);
		$this->room = ($this->roomId == 0 ? false : (new ManagementRoom)->get($this->roomId)[0]);
		return $this;
	}
}
