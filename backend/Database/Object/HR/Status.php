<?php

namespace Database\Object\HR;

use Security\CustomObject;

class Status extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN,
    ];
}
