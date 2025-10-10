<?php

namespace Database\Object\Absent;

use Database\Interface\CustomObject;

class Payment extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
    ];
}
