<?php

namespace Database\Object\EHBO;

use Database\Interface\CustomObject;

class Description extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
