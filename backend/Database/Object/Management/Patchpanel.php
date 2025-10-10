<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class Patchpanel extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "buildingId" => self::TYPE_INTEGER,
        "roomId" => self::TYPE_INTEGER,
        "cabinetId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "patchpoints" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class],
        "room" => ["roomId" => \Database\Repository\Management\Room::class],
        "cabinet" => ["cabinetId" => \Database\Repository\Management\Cabinet::class]
    ];

    public function init()
    {
        $this->formatted->full = "{$this->linked->cabinet->formatted->full} - {$this->name}";
    }
}
