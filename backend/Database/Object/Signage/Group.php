<?php

namespace Database\Object\Signage;

use Database\Interface\CustomObject;

class Group extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => [
            "schoolId" => \Database\Repository\School\School::class
        ]
    ];
}
