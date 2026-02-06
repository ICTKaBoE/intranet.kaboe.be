<?php

namespace Database\Object\EHBO;

use Security\CustomObject;

class Description extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING
    ];
}
