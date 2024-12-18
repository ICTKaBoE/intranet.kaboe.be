<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

class EmployeeOwnfield extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatEmployeeId" => "int",
        "informatGuid" => "string",
        "name" => "string",
        "value" => "string",
        "type" => "string",
        "section" => "int"
    ];
}
