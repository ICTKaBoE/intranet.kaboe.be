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
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class]
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
        $lastLogOn = (new ComputerUsageLogOn)->getByComputerId($this->id);

        if (!$lastLogOn) $this->formatted->lastUsage = null;
        else {
            $lastLogOn = Arrays::firstOrNull(array_reverse($lastLogOn));
            $this->formatted->lastUsage = Clock::at($lastLogOn->logon)->format("d/m/Y") . " - {$lastLogOn->username}";
        }
    }
}
