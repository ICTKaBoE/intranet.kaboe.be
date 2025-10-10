<?php

namespace Database\Object\Violence;

use Database\Interface\CustomObject;

class Intention extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
