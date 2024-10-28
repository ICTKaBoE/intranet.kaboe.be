<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class MSwitch extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "buildingId" => "int",
        "roomId" => "int",
        "cabinetId" => "int",
        "name" => "string",
        "serialnumber" => "string",
        "macaddress" => "string",
        "ports" => "int",
        "manufacturer" => "string",
        "model" => "string",
        "ip" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class],
        "room" => ["roomId" => \Database\Repository\Management\Room::class],
        "cabinet" => ["cabinetId" => \Database\Repository\Management\Cabinet::class]
    ];

    public function init()
    {
        $this->formatted->macaddress = CString::formatMacAddress($this->macaddress);
        $this->formatted->ip = CString::formatLink($this->ip);
        $this->formatted->manModel = "{$this->manufacturer} {$this->model}";
    }
}
