<?php

namespace Database\Object\Signage;

use Database\Interface\CustomObject;

class Screen extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "code" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "groupId" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "group" => ['groupId' => \Database\Repository\Signage\Group::class]
    ];
}
