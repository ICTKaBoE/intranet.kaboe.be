<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;

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
        $this->formatted->icon->type = "<i class=\"ti ti-device-" . ($this->type == "L" ? "laptop" : "desktop") . "\"></i>";
        $this->formatted->os = "{$this->osType} {$this->osVersion}";
        $this->formatted->manModel = "{$this->manufacturer} {$this->model}";
    }
}
