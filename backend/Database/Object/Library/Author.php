<?php

namespace Database\Object\Library;

use Database\Interface\CustomObject;

class Author extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "name" => "string",
        "deleted" => "boolean"
    ];
}
