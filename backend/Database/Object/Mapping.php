<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Mapping extends CustomObject
{
    protected $objectAttributes = [
        "key" => "string",
        "value" => "string"
    ];
}
