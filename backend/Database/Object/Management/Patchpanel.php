<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class Patchpanel extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "buildingId" => "int",
        "roomId" => "int",
        "cabinetId" => "int",
        "name" => "string",
        "patchpoints" => "int",
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
        $this->formatted->full = "{$this->linked->cabinet->formatted->full} - {$this->name}";
    }
}
