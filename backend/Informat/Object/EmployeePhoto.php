<?php

namespace Informat\Object;

use Security\CustomObject;

class EmployeePhoto extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "personId" => self::TYPE_STRING,
        "photo" => self::TYPE_BASE64
    ];
}
