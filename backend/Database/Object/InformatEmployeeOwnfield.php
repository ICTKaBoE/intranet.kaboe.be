<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class InformatEmployeeOwnfield extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatId" => "string",
        "informatEmployeeId" => "int",
        "name" => "string",
        "value" => "string"
    ];
}
