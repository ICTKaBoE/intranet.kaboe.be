<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class BikePrice extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "validFrom" => "date",
        "validUntil" => "date",
        "amount" => "double",
        "deleted" => "boolean"
    ];
}
