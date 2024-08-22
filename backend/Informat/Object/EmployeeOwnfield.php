<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class EmployeeOwnfield extends CustomObject
{
    protected $objectAttributes = [
        "personId" => "string",
        "vvId" => "string",
        "naam" => "string",
        "waarde" => "string"
    ];
}
