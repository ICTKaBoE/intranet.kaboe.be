<?php

namespace Informat\REST\Object;

use Informat\REST\Interface\CustomObject;

class EmployeeOwnfield extends CustomObject
{
    protected $objectAttributes = [
        "personId" => "string",
        "vvId" => "string",
        "naam" => "string",
        "waarde" => "string"
    ];
}
