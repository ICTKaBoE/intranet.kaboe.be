<?php

namespace Database\Object\Management;

use Security\CustomObject;
use Helpers\CString;

class Treshhold extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "type" => self::TYPE_STRING,
        "min" => self::TYPE_DOUBLE,
        "max" => self::TYPE_DOUBLE,
        "color" => self::TYPE_STRING
    ];
}
