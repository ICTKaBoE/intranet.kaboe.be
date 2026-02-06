<?php

namespace Informat\Object;

use Security\CustomObject;

class EmployeePhoto extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "personId" => "string",
        "photo" => "base64"
    ];
}
