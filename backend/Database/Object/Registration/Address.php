<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;

class Address extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "registrationId" => "int",
        "followNumber" => "int",
        "communication" => "boolean",
        "street" => "string",
        "number" => "int",
        "bus" => "string",
        "zipcode" => "string",
        "city" => "string",
        "countryId" => "int",
        "deleted" => "boolean"
    ];
}
