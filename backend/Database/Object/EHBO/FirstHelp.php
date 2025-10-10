<?php

namespace Database\Object\EHBO;

use Database\Interface\CustomObject;

class FirstHelp extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
