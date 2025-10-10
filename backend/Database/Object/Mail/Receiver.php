<?php

namespace Database\Object\Mail;

use Database\Interface\CustomObject;

class Receiver extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "mailId" => self::TYPE_INTEGER,
        "email" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
