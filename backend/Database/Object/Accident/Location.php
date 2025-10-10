<?php

namespace Database\Object\Accident;

use Database\Interface\CustomObject;

class Location extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "categoryId" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "order" => self::TYPE_INTEGER
    ];
}
