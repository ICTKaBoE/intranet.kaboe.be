<?php

namespace Database\Object\Violence;

use Database\Interface\CustomObject;

class Damage extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
