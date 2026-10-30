<?php

namespace Database\Object\School;

use Ouzo\Utilities\Arrays;
use Security\CustomObject;

class Department extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "managementRoomId" => self::TYPE_STRING,
        "informatClassId" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
    ];
}
