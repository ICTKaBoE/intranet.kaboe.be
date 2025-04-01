<?php

namespace Database\Object\Bike;

use Database\Interface\CustomObject;

class Price extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "validFrom" => "date",
        "validUntil" => "date",
        "amount" => "double",
        "deleted" => "boolean"
    ];
}
