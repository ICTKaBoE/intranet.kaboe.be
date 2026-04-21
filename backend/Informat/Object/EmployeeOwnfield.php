<?php

namespace Informat\Object;

use Security\CustomObject;

class EmployeeOwnfield extends CustomObject
{
    protected $objectAttributes = [
        "personId" => self::TYPE_STRING,
        "vvId" => self::TYPE_STRING,
        "naam" => self::TYPE_STRING,
        "waarde" => self::TYPE_STRING,
        "dataType" => self::TYPE_STRING,
        "rubriek" => self::TYPE_INTEGER
    ];
}
