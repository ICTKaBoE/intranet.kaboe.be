<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class Room extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "buildingId" => "int",
        "floor" => "int",
        "number" => "int",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class]
    ];

    public function init()
    {
        $this->formatted->number = CString::leadingZeros($this->number, 2);
        $this->formatted->name = "{$this->floor}.{$this->formatted->number}";
        $this->formatted->full = "{$this->linked->building->formatted->full} - {$this->formatted->name}";
    }
}
