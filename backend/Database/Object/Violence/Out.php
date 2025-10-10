<?php

namespace Database\Object\Violence;

use Database\Interface\CustomObject;

class Out extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
