<?php

namespace Database\Object;

use Ouzo\Utilities\Strings;
use Database\Repository\School;
use Database\Interface\CustomObject;
use Database\Repository\ManagementIpad;
use Database\Repository\ManagementComputer;

class ManagementCart extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"name",
        "type",
		"deleted"
	];

	public function init()
	{
		$this->_orderfield = "{$this->schoolId}-{$this->name}";
		$this->typeIcon = "device-" . (Strings::equal($this->type, "L") ? "laptop" : "ipad");
	}

	public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
		if ($this->type == "L") {
			$this->devices = ($this->id == 0 ? false : (new ManagementComputer)->getBySchoolAndCart($this->schoolId, $this->id));
			$this->devicelist = $this->name . ": ";
			if (!empty($this->devices)) {
				foreach($this->devices as $_device) {
					$this->devicelist .= $_device->name . " ";
				}
			} else $this->devicelist .= "leeg";
		} else if ($this->type == 'I') {
			$this->devices = ($this->id == 0 ? false : (new ManagementIpad)->getBySchoolAndCart($this->schoolId, $this->id));
			$this->devicelist = $this->name . ": ";
			if (!empty($this->devices)) {
				$this->devicelist = $this->name . ": ";
				foreach($this->devices as $_device) {
					$this->devicelist .= $_device->deviceName . " ";
				}
			} else $this->devicelist .= "leeg";
		}
		
		return $this;
	}
}