<?php

namespace Database\Object\Bike;

use Security\CustomObject;

class DistanceType extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
    ];
}
