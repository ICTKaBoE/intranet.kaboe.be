<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class UserLoginHistory extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "userId" => "int",
        "source" => "string",
        "timestamp" => "datetime"
    ];

    protected $linkedAttributes = [
        "user" => ["userId" => \Database\Repository\User::class]
    ];
}
