<?php

namespace Informat\Object;

use Security\CustomObject;

class StudentPhoto extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "persoonId" => self::TYPE_STRING,
        "foto" => self::TYPE_BASE64
    ];
}
