<?php

namespace Cron;

use JAMF\Repository\Device;
use Database\Repository\ManagementIpad;
use Database\Object\ManagementIpad as ObjectManagementIpad;

abstract class CronJamf
{
	public static function Sync()
	{
		$items = (new Device)->get();
		$ipadRepo = new ManagementIpad;

		foreach ($items as $device) {
			$ipad = $ipadRepo->getByUDID($device->UDID) ?? new ObjectManagementIpad;
			$ipad->udId = $device->UDID;
			$ipad->serialNumber = $device->serialNumber;
			$ipad->modelName = $device->model['name'];
			$ipad->osPrefix = $device->os['prefix'];
			$ipad->osVersion = $device->os['version'];
			$ipad->deviceName = $device->name;
			$ipad->schoolId = $device->locationId;
			$ipad->batteryLevel = $device->batteryLevel;
			$ipad->totalCapacity = $device->totalCapacity;
			$ipad->availableCapacity = $device->availableCapacity;
			$ipad->lastCheckin = $device->lastCheckin;

			$ipadRepo->set($ipad);
		}
	}
}
