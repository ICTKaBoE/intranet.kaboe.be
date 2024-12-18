<?php

namespace Database\Object\Management;

use Helpers\HTML;
use Helpers\CString;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;

class ComputerBattery extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "computerId" => "int",
        "batteryId" => "string",
        "lastCheck" => "datetime",
        "designCapacity" => "int",
        "fullChargeCapacity" => "int",
        "cycleCount" => "int",
        "deleted" => "boolean"
    ];

    public function init()
    {
        $this->capacity = ($this->designCapacity != 0 ? ($this->fullChargeCapacity * 100) / $this->designCapacity : 0);

        $this->formatted->capacity = CString::formatNumber(($this->designCapacity != 0 ? ($this->fullChargeCapacity * 100) / $this->designCapacity : 0), 2) . "%";
        $this->formatted->designCapacity = CString::formatNumber($this->designCapacity, 0) . ($this->designCapacity < 4400 ? " mAh" : " mWh");
        $this->formatted->fullChargeCapacity = CString::formatNumber($this->fullChargeCapacity, 0) . ($this->fullChargeCapacity < 4400 ? " mAh" : " mWh");
        $this->formatted->lastCheck = Clock::at($this->lastCheck)->format("d/m/Y H:i:s");

        $this->createCapacityBadge();
    }

    private function createCapacityBadge()
    {
        $settings = Arrays::first((new Navigation)->getByParentIdAndLink(0, "management"))->settings['computer']['batteryTreshhold'];
        $level = Arrays::first(Arrays::filter($settings, fn($s) => $this->capacity <= $s['max'] && $this->capacity >= $s['min']));

        $this->formatted->badge->capacity = HTML::Badge($this->formatted->capacity, backgroundColor: $level['color']);
    }
}
