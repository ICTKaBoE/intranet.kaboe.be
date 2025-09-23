<?php

namespace Database\Object\Accident;

use Database\Interface\CustomObject;

class Party extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
    ];
}
