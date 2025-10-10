<?php

namespace Database\Object\General;

use Database\Interface\CustomObject;

class Nationality extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "code" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
