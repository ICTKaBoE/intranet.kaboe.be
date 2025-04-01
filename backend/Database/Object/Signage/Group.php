<?php

namespace Database\Object\Signage;

use Database\Interface\CustomObject;

class Group extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "name" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => [
            "schoolId" => \Database\Repository\School\School::class
        ]
    ];
}
