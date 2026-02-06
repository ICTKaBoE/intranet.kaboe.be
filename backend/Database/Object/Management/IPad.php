<?php

namespace Database\Object\Management;

use Helpers\HTML;
use Helpers\CString;
use Ouzo\Utilities\Arrays;
use Security\CustomObject;
use Database\Repository\Management\Treshhold;
use Database\Repository\Navigation\Navigation;

class IPad extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "jamfId" => self::TYPE_STRING,
        "serialnumber" => self::TYPE_STRING,
        "model" => self::TYPE_STRING,
        "osPrefix" => self::TYPE_STRING,
        "osVersion" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "batteryLevel" => self::TYPE_INTEGER,
        "totalCapacity" => self::TYPE_DOUBLE,
        "availableCapacity" => self::TYPE_DOUBLE,
        "lastCheckin" => self::TYPE_DATETIME,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class]
    ];

    public function init()
    {
        $this->formatted->os = "{$this->osPrefix} {$this->osVersion}";
        $this->formatted->batteryLevel = "{$this->batteryLevel}%";
        $this->formatted->totalCapacity = "{$this->totalCapacity}GB";
        $this->formatted->availableCapacity = CString::formatNumber($this->availableCapacity, 2) . "GB";

        $this->capacity = ($this->availableCapacity == 0 || $this->totalCapacity == 0) ? 0 : ($this->availableCapacity / $this->totalCapacity) * 100;
        $this->formatted->capacity = CString::formatNumber($this->availableCapacity, 2) . "GB / " . CString::formatNumber($this->totalCapacity, 2) . "GB";
        $this->formatted->capacityPercentage = CString::formatNumber($this->capacity, 2) . "%";

        $this->createBatteryBadge();
        $this->createCapacityBadge();
    }

    private function createBatteryBadge()
    {
        $level = (new Treshhold)->getByTypeAndPercentageBetween("ipad.battery", $this->batteryLevel);
        $this->formatted->badge->battery = HTML::Badge($this->formatted->batteryLevel, backgroundColor: $level->color);
    }

    private function createCapacityBadge()
    {
        $level = (new Treshhold)->getByTypeAndPercentageBetween("ipad.capacity", $this->capacity);
        $this->formatted->badge->capacity = HTML::Badge($this->formatted->capacityPercentage, backgroundColor: $level->color);
    }
}
