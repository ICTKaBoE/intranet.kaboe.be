<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class CCTV extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "buildingId" => "int",
        "name" => "string",
        "serialnumber" => "string",
        "macaddress" => "string",
        "manufacturer" => "string",
        "model" => "string",
        "ip" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class]
    ];

    public function init()
    {
        $this->formatted->macaddress = CString::formatMacAddress($this->macaddress);
        $this->formatted->ip = CString::formatLink($this->ip);
        $this->formatted->manModel = "{$this->manufacturer} {$this->model}";
    }
}
