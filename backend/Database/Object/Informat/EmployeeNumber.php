<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;
use Helpers\HTML;

class EmployeeNumber extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatEmployeeId" => "int",
        "informatGuid" => "string",
        "number" => "string",
        "type" => "string",
        "category" => "string"
    ];
}
