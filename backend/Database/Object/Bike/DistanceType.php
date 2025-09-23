<?php

namespace Database\Object\Bike;

use Database\Interface\CustomObject;

class DistanceType extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
    ];
}
