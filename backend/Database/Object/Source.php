<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Source extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "tokenType" => self::TYPE_STRING,
        "tokenUntil" => self::TYPE_DATETIME,
        "tokenValue" => self::TYPE_STRING,
        "identityEndpoint" => self::TYPE_STRING,
        "identityGrantType" => self::TYPE_STRING,
        "identityClientId" => self::TYPE_STRING,
        "identityClientSecret" => self::TYPE_STRING,
        "identityScope" => self::TYPE_STRING
    ];
}
