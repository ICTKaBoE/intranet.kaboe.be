<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Notification extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "userId" => self::TYPE_INTEGER,
        "showtime" => self::TYPE_DATETIME,
        "type" => self::TYPE_STRING,
        "link" => self::TYPE_STRING,
        "delay" => self::TYPE_INTEGER,
        "message" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
