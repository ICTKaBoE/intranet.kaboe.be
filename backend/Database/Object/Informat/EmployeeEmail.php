<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

class EmployeeEmail extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatEmployeeId" => "int",
        "informatGuid" => "string",
        "email" => "string",
        "type" => "string"
    ];
}
