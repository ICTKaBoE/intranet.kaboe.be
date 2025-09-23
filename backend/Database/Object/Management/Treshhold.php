<?php

namespace Database\Object\Management;

use Database\Interface\CustomObject;
use Helpers\CString;

class Treshhold extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "type" => "string",
        "min" => "double",
        "max" => "double",
        "color" => "string"
    ];
}
