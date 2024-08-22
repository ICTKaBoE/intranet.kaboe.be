<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class InformatEmployeeNumber extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatId" => "string",
        "informatEmployeeId" => "int",
        "type" => "string",
        "kind" => "string",
        "number" => "string"
    ];
}
