<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class RouteGroup extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "name" => "string",
        "domain" => "string",
        "prefix" => "string",
        "controller" => "string",
        "middleware" => "string",
        "deleted" => "boolean"
    ];
}
