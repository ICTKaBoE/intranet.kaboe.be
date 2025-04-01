<?php

namespace Database\Object\Route;

use Database\Interface\CustomObject;

class Group extends CustomObject
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
