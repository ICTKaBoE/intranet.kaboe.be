<?php

namespace Database\Object\Violence;

use Security\CustomObject;

class Intention extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING
    ];
}
