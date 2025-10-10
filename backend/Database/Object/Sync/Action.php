<?php

namespace Database\Object\Sync;

use Database\Interface\CustomObject;

class Action extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "color" => self::TYPE_STRING
    ];
}
