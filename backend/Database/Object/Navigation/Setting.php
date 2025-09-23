<?php

namespace Database\Object\Navigation;

use Database\Interface\CustomObject;

class Setting  extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "navigationId" => "int",
        "key" => "string",
        "value" => "*"
    ];
}
