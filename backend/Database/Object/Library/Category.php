<?php

namespace Database\Object\Library;

use Database\Interface\CustomObject;

class Category extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "name" => "string",
        "deleted" => "boolean"
    ];
}
