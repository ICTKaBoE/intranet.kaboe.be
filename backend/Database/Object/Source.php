<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Source extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "tokenType" => "string",
        "tokenUntil" => "datetime",
        "tokenValue" => "string",
        "identityEndpoint" => "string",
        "identityGrantType" => "string",
        "identityClientId" => "string",
        "identityClientSecret" => "string",
        "identityScope" => "string"
    ];
}
