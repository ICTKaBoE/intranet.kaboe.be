<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class Cabinet extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "buildingId" => self::TYPE_INTEGER,
        "roomId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class],
        "room" => ["roomId" => \Database\Repository\Management\Room::class]
    ];

    public function init()
    {
        $this->formatted->full = "{$this->linked->room->formatted->full} - {$this->name}";
    }
}
