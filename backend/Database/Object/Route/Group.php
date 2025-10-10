<?php

namespace Database\Object\Route;

use Database\Interface\CustomObject;

class Group extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "domain" => self::TYPE_STRING,
        "prefix" => self::TYPE_STRING,
        "controller" => self::TYPE_STRING,
        "middleware" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
