<?php

namespace Database\Object\General;

use Security\CustomObject;

class Nationality extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "code" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
