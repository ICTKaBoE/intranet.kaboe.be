<?php

namespace Database\Object\Absent;

use Database\Interface\CustomObject;

class Payment extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
    ];
}
