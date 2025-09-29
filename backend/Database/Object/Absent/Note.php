<?php

namespace Database\Object\Absent;

use Database\Interface\CustomObject;

class Note extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
    ];
}
