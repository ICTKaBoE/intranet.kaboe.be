<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class EmployeePhoto extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "personId" => "string",
        "photo" => "base64"
    ];
}
