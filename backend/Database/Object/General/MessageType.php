<?php

namespace Database\Object\General;

use Database\Interface\CustomObject;

class MessageType extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
    ];
}
