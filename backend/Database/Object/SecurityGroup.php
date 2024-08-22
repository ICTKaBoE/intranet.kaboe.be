<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class SecurityGroup extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "name" => "string",
        "permission" => "binary"
    ];
}
