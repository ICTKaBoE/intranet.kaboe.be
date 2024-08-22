<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class SecurityGroupUser extends CustomObject
{
    protected $objectAttributes = [
        "securityGroupId" => "int",
        "userId" => "int"
    ];
}
