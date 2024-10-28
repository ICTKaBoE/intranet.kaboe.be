<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;
use Ouzo\Utilities\Clock;

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

    protected $linkedAttributes = [
        "computer" => ["computerId" => \Database\Repository\Management\Computer::class],
    ];

    public function init()
    {
        $this->formatted->capacity = CString::formatNumber(($this->designCapacity != 0 ? ($this->fullChargeCapacity * 100) / $this->designCapacity : 0), 2) . "%";
        $this->formatted->designCapacity = CString::formatNumber($this->designCapacity, 0) . ($this->designCapacity < 4400 ? " mAh" : " mWh");
        $this->formatted->fullChargeCapacity = CString::formatNumber($this->fullChargeCapacity, 0) . ($this->fullChargeCapacity < 4400 ? " mAh" : " mWh");
        $this->formatted->lastCheck = Clock::at($this->lastCheck)->format("d/m/Y H:i:s");
    }
}
