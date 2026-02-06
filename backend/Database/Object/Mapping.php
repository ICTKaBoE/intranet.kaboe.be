<?php

namespace Database\Object;

use Security\CustomObject;

class Mapping extends CustomObject
{
    protected $objectAttributes = [
        "key" => self::TYPE_STRING,
        "value" => self::TYPE_STRING
    ];
}
