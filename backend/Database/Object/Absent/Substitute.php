<?php

namespace Database\Object\Absent;

use Database\Interface\CustomObject;

class Substitute extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
    ];
}
