<?php

namespace Database\Object\Accident;

use Database\Interface\CustomObject;

class Location extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "categoryId" => "string",
        "name" => "string",
        "order" => "int"
    ];
}
