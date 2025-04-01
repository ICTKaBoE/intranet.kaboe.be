<?php

namespace Database\Object\User;

use Database\Interface\CustomObject;

class LoginHistory extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "userId" => "int",
        "source" => "string",
        "timestamp" => "datetime"
    ];

    protected $linkedAttributes = [
        // "user" => ["userId" => \Database\Repository\User\User::class]
    ];
}
