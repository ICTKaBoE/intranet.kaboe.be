<?php

namespace Database\Object\Mail;

use Security\CustomObject;

class Attachment extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "mailId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "path" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
