<?php

namespace Database\Object\Accident;

use Security\CustomObject;

class Party extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "extendedOptions" => self::TYPE_STRING,
    ];
}
