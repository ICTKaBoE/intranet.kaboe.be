<?php

namespace Database\Object\Management;

use Security\CustomObject;
use Database\Repository\Management\ComputerBattery;
use Database\Repository\Management\ComputerUsageLogOn;
use Database\Repository\Management\ComputerUsageOnOff;
use Helpers\HTML;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class Computer extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "entraId" => self::TYPE_STRING,
        "schoolId" => self::TYPE_INTEGER,
        "type" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "orderId" => self::TYPE_STRING,
        "enrollmentProfileName" => self::TYPE_STRING,
        "osType" => self::TYPE_STRING,
        "osVersion" => self::TYPE_STRING,
        "manufacturer" => self::TYPE_STRING,
        "model" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class]
    ];

    public function init()
    {
        $this->formatted->icon->type = HTML::Icon("device-" . ($this->type == "L" ? "laptop" : "desktop"));
        $this->formatted->os = "{$this->osType} {$this->osVersion}";
        $this->formatted->manModel = "{$this->manufacturer} {$this->model}";
    }
}
