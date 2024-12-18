<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

class EmployeeAddress extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatEmployeeId" => "int",
        "informatGuid" => "string",
        "street" => "string",
        "number" => "int",
        "bus" => "string",
        "zipcode" => "string",
        "city" => "string",
        "countryId" => "int",
        "current" => "bool"
    ];
}
