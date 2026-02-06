<?php

namespace Database\Object;

use Security\CustomObject;

class Source extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "host" => self::TYPE_STRING,
        "tokenType" => self::TYPE_STRING,
        "tokenUntil" => self::TYPE_DATETIME,
        "tokenValue" => self::TYPE_STRING,
        "identityGrantType" => self::TYPE_STRING,
        "identityClientId" => self::TYPE_STRING,
        "identityClientSecret" => self::TYPE_STRING,
        "identityScope" => self::TYPE_STRING,
        "password" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
