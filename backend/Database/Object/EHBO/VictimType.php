<?php

namespace Database\Object\EHBO;

use Security\CustomObject;

class VictimType extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "type" => self::TYPE_STRING,
    ];
}
