<?php

namespace Database\Object;

use Security\CustomObject;

class Cron extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "expression" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "class" => self::TYPE_STRING,
        "function" => self::TYPE_STRING,
        "active" => self::TYPE_BOOLEAN,
        "lastRun" => self::TYPE_DATETIME
    ];
}
