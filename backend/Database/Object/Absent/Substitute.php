<?php

namespace Database\Object\Absent;

use Security\CustomObject;

class Substitute extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
    ];
}
