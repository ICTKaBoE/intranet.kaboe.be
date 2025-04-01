<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;

class Building extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "name" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class]
    ];

    public function init()
    {
        $this->formatted->full = "{$this->linked->school->name} - {$this->name}";
    }
}
