<?php

namespace Database\Object\Smartschool;

use Security\CustomObject;

class Message extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "sourceId" => self::TYPE_STRING,
        "subject" => self::TYPE_STRING,
        "body" => self::TYPE_STRING,
        "sendAfterDateTime" => self::TYPE_DATETIME,
        "sentDateTime" => self::TYPE_DATETIME,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
