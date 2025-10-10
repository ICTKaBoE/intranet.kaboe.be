<?php

namespace Database\Object\Navigation;

use Database\Interface\CustomObject;

class Setting  extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "navigationId" => self::TYPE_INTEGER,
        "key" => self::TYPE_STRING,
        "value" => self::TYPE_ALL
    ];
}
