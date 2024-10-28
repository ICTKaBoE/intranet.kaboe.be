<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class Cabinet extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "buildingId" => "int",
        "roomId" => "int",
        "name" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class],
        "room" => ["roomId" => \Database\Repository\Management\Room::class]
    ];

    public function init()
    {
        $this->formatted->full = "{$this->linked->room->formatted->full} - {$this->name}";
    }
}
