<?php

namespace Database\Object\General;

use Database\Interface\CustomObject;

class Nationality extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "code" => "string",
        "name" => "string"
    ];
}
