<?php

namespace Database\Object\Mail;

use Database\Interface\CustomObject;

class Mail extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "subject" => self::TYPE_STRING,
        "body" => self::TYPE_STRING,
        "html" => self::TYPE_BOOLEAN,
        "replyTo" => self::TYPE_JSON,
        "sendAfterDateTime" => self::TYPE_DATETIME,
        "sentDateTime" => self::TYPE_DATETIME,
        "error" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
