<?php

namespace Database\Object\Violence;

use Database\Interface\CustomObject;

class DamageKind extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
