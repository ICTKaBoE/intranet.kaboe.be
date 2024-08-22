<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class InformatEmployeeAddress extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatId" => "string",
        "informatEmployeeId" => "int",
        "street" => "string",
        "number" => "int",
        "bus" => "string",
        "zipcode" => "string",
        "city" => "string",
        "countryNISCode" => "int",
        "current" => "bool"
    ];
}
