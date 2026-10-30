<?php

namespace Database\Object\School;

use Security\CustomObject;

class DepartmentRoom extends CustomObject
{
    protected $objectAttributes = [
        "departmentId" => self::TYPE_INTEGER,
        "roomId" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "room" => ["roomId" => \Database\Repository\Management\Room::class]
    ];
}
