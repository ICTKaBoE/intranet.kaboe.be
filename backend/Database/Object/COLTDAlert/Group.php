<?php

namespace Database\Object\COLTDAlert;

use Security\CustomObject;

class Group extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "name" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
