<?php

namespace Database\Object\Library;

use Security\CustomObject;

class Author extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "name" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
