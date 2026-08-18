<?php

namespace Database\Object\Management;

use Security\CustomObject;
use Helpers\CString;

class Room extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "buildingId" => self::TYPE_INTEGER,
        "floor" => self::TYPE_INTEGER,
        "number" => self::TYPE_INTEGER,
        "alias" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class]
    ];

    public function init()
    {
        $this->formatted->number = CString::leadingZeros($this->number, 2);
        $this->formatted->name = "{$this->floor}.{$this->formatted->number}";
        $this->formatted->buildingRoom = "{$this->linked->building->name} {$this->formatted->name}";
        $this->formatted->full = "{$this->linked->building->formatted->full} - {$this->formatted->name}" . ($this->alias ? " ({$this->alias})" : "");
    }
}
