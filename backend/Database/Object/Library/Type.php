<?php

namespace Database\Object\Library;

use Database\Interface\CustomObject;

class Type extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string"
    ];
}
