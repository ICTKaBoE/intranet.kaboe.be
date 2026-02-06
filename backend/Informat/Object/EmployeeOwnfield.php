<?php

namespace Informat\Object;

use Security\CustomObject;

class EmployeeOwnfield extends CustomObject
{
    protected $objectAttributes = [
        "personId" => "string",
        "vvId" => "string",
        "naam" => "string",
        "waarde" => "string",
        "dataType" => "string",
        "rubriek" => "int"
    ];
}
