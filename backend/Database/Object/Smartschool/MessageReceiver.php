<?php

namespace Database\Object\Smartschool;

use Security\CustomObject;

class MessageReceiver extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "messageId" => self::TYPE_INTEGER,
        "username" => self::TYPE_STRING,
        "account" => self::TYPE_INTEGER,
        "copyToLvs" => self::TYPE_BOOLEAN,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
