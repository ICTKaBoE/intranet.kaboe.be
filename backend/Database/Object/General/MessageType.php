<?php

namespace Database\Object\General;

use Security\CustomObject;

class MessageType extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
    ];
}
