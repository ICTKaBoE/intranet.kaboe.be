<?php

namespace Database\Object\User;

use Security\CustomObject;

class LoginHistory extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "userId" => self::TYPE_INTEGER,
        "source" => self::TYPE_STRING,
        "timestamp" => self::TYPE_DATETIME
    ];

    protected $linkedAttributes = [
        // "user" => ["userId" => \Database\Repository\User\User::class]
    ];
}
