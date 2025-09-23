<?php

namespace Database\Object\TempReg;

use Database\Interface\CustomObject;

class Treshhold extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "min" => "double",
        "max" => "double",
        "color" => "string"
    ];
}
