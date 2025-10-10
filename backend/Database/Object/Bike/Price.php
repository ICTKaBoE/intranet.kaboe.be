<?php

namespace Database\Object\Bike;

use Database\Interface\CustomObject;

class Price extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "validFrom" => self::TYPE_DATE,
        "validUntil" => self::TYPE_DATE,
        "amount" => self::TYPE_DOUBLE,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
