<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class AccessPoint extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "buildingId" => self::TYPE_INTEGER,
        "roomId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "serialnumber" => self::TYPE_STRING,
        "macaddress" => self::TYPE_STRING,
        "manufacturer" => self::TYPE_STRING,
        "model" => self::TYPE_STRING,
        "ip" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class],
        "room" => ["roomId" => \Database\Repository\Management\Room::class]
    ];

    public function init()
    {
        $this->formatted->macaddress = CString::formatMacAddress($this->macaddress);
        $this->formatted->ip = CString::formatLink($this->ip);
        $this->formatted->manModel = "{$this->manufacturer} {$this->model}";
    }
}
