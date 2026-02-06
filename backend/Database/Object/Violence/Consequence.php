<?php

namespace Database\Object\Violence;

use Security\CustomObject;

class Consequence extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING
    ];
}
