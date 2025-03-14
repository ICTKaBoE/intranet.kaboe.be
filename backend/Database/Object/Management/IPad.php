<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Database\Repository\Navigation;
use Helpers\CString;
use Helpers\HTML;
use Ouzo\Utilities\Arrays;

class IPad extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "jamfId" => "string",
        "serialnumber" => "string",
        "model" => "string",
        "osPrefix" => "string",
        "osVersion" => "string",
        "name" => "string",
        "batteryLevel" => "int",
        "totalCapacity" => "double",
        "availableCapacity" => "double",
        "lastCheckin" => "datetime",
        "deleted" => "boolean"
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
        $settings = Arrays::first((new Navigation)->getByParentIdAndLink(0, "management"))->settings['ipad']['batteryTreshhold'];
        $level = Arrays::first(Arrays::filter($settings, fn($s) => $this->batteryLevel <= $s['max'] && $this->batteryLevel >= $s['min']));

        $this->formatted->badge->battery = HTML::Badge($this->formatted->batteryLevel, backgroundColor: $level['color']);
    }

    private function createCapacityBadge()
    {
        $settings = Arrays::first((new Navigation)->getByParentIdAndLink(0, "management"))->settings['ipad']['capacityTreshhold'];
        $level = Arrays::first(Arrays::filter($settings, fn($s) => $this->capacity <= $s['max'] && $this->capacity >= $s['min']));

        $this->formatted->badge->capacity = HTML::Badge($this->formatted->capacityPercentage, backgroundColor: $level['color']);
    }
}
