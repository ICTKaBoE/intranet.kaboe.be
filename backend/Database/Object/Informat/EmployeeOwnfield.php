<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

class EmployeeOwnfield extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatEmployeeId" => self::TYPE_INTEGER,
        "informatGuid" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "value" => self::TYPE_STRING,
        "type" => self::TYPE_STRING,
        "section" => self::TYPE_INTEGER
    ];
}
