<?php

namespace Database\Object\Bike;

use Database\Interface\CustomObject;

class DistanceType extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
    ];
}
