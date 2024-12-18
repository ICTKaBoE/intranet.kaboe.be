<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Database\Repository\Management\ComputerBattery;
use Database\Repository\Management\ComputerUsageLogOn;
use Database\Repository\Management\ComputerUsageOnOff;
use Helpers\HTML;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class Computer extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "entraId" => "string",
        "schoolId" => "int",
        "type" => "string",
        "name" => "string",
        "orderId" => "string",
        "enrollmentProfileName" => "string",
        "osType" => "string",
        "osVersion" => "string",
        "manufacturer" => "string",
        "model" => "string",
        "batteryError" => "boolean",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School::class]
    ];

    public function init()
    {
        $this->formatted->icon->type = HTML::Icon("device-" . ($this->type == "L" ? "laptop" : "desktop"));
        $this->formatted->os = "{$this->osType} {$this->osVersion}";
        $this->formatted->manModel = "{$this->manufacturer} {$this->model}";

        $this->getBatteries();
        $this->getLastUsage();
    }

    private function getBatteries()
    {
        $batteries = (new ComputerBattery)->getByComputerId($this->id);
        $this->linked->batteries = $batteries;

        $this->formatted->badge->capacity = join("<br />", Arrays::map($batteries, fn($b) => $b->formatted->badge->capacity));
    }

    private function getLastUsage()
    {
        $lastOnOff = Arrays::firstOrNull(array_reverse((new ComputerUsageOnOff)->getByComputerId($this->id)));
        if (!$lastOnOff) $this->formatted->lastUsage = null;
        else {
            $lastLogOn = Arrays::last((new ComputerUsageLogOn)->getByComputerIdAndLogonBetweenStartupAndShutdown($this->id, $lastOnOff->startup, $lastOnOff->shutdown));
            if (!$lastLogOn) $this->formatted->lastUsage = null;
            else $this->formatted->lastUsage = Clock::at($lastOnOff->startup)->format("d/m/Y") . " - {$lastLogOn->username}";
        }
    }
}
