<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class Firewall extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "buildingId" => "int",
        "roomId" => "int",
        "cabinetId" => "int",
        "hostname" => "string",
        "manufacturer" => "string",
        "model" => "string",
        "serialnumber" => "string",
        "macaddress" => "string",
        "ip" => "url",
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
