<?php

namespace Database\Object;

use Database\Repository\School;
use Database\Interface\CustomObject;
use Database\Repository\ManagementCart;

class ManagementIpad extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"cartId",
		"type",
		"udId",
		"serialNumber",
		"modelName",
		"osPrefix",
		"osVersion",
		"deviceName",
		"batteryLevel",
		"totalCapacity",
		"availableCapacity",
		"lastCheckin",
		"deleted"
	];

	public function init()
	{
		$this->osDescription = "{$this->osPrefix} {$this->osVersion}";

		$this->batteryLevel = floatval($this->batteryLevel ?? 0);
		$this->totalCapacity = floatval($this->totalCapacity ?? 0);
		$this->availableCapacity = floatval($this->availableCapacity ?? 0);
		$this->availablePercentage = ($this->availableCapacity == 0 || $this->totalCapacity == 0) ? 0 : ($this->availableCapacity / $this->totalCapacity) * 100;

		$this->batteryLevelPercentage = ($this->batteryLevel * 100) . "%";
		$this->batteryLevelColor = ($this->batteryLevel > 0.5 ? 'green' : ($this->batteryLevel > 0.2 ? 'orange' : 'red'));

		$this->totalCapacityLabel = number_format($this->totalCapacity, 2, ",", ".") . "GB";
		$this->availableCapacityLabel = number_format($this->availableCapacity, 2, ",", ".") . "GB";
		$this->capacityFormatted = "{$this->availableCapacityLabel} / {$this->totalCapacityLabel}";

		$this->availablePercentageLabel = number_format($this->availablePercentage, 2, ",", ".") . "%";
		$this->availablePercentageColor = ($this->availablePercentage > 0.5 ? 'green' : ($this->availablePercentage > 0.2 ? 'orange' : 'red'));

		$this->_orderfield = "{$this->schoolId}-{$this->deviceName}";
	}

	public function link()
	{
		$this->school = ($this->schoolId == 0 ? false : (new School)->get($this->schoolId)[0]);
		$this->cart = ($this->cartId == 0 ? false : (new ManagementCart)->get($this->cartId)[0]);
		return $this;
	}
}
