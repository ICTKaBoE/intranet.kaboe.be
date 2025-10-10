<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;

class Address extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "registrationId" => self::TYPE_INTEGER,
        "followNumber" => self::TYPE_INTEGER,
        "communication" => self::TYPE_BOOLEAN,
        "street" => self::TYPE_STRING,
        "number" => self::TYPE_INTEGER,
        "bus" => self::TYPE_STRING,
        "zipcode" => self::TYPE_STRING,
        "city" => self::TYPE_STRING,
        "countryId" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
