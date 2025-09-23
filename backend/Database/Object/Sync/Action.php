<?php

namespace Database\Object\Sync;

use Database\Interface\CustomObject;

class Action extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
        "color" => "string"
    ];
}
