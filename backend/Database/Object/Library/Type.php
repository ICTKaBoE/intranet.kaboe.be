<?php

namespace Database\Object\Library;

use Security\CustomObject;

class Type extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
