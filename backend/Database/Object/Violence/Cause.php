<?php

namespace Database\Object\Violence;

use Security\CustomObject;

class Cause extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING
    ];
}
