<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Mapping extends CustomObject
{
    protected $objectAttributes = [
        "key" => self::TYPE_STRING,
        "value" => self::TYPE_STRING
    ];
}
