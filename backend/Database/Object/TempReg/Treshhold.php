<?php

namespace Database\Object\TempReg;

use Security\CustomObject;

class Treshhold extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "min" => self::TYPE_DOUBLE,
        "max" => self::TYPE_DOUBLE,
        "color" => self::TYPE_STRING
    ];
}
