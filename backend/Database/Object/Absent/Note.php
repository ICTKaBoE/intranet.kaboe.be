<?php

namespace Database\Object\Absent;

use Security\CustomObject;

class Note extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
    ];
}
