<?php

namespace Database\Object\Accident;

use Database\Interface\CustomObject;

class Party extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
    ];
}
