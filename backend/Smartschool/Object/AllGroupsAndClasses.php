<?php

namespace Smartschool\Object;

use Security\CustomObject;

class AllGroupsAndClasses extends CustomObject
{
    protected $objectAttributes = [
        "name" => self::TYPE_STRING,
        "desc" => self::TYPE_STRING,
        "type" => self::TYPE_STRING,
        "code" => self::TYPE_STRING,
        "untis" => self::TYPE_STRING,
        "visible" => self::TYPE_DATE,
        "isOfficial" => self::TYPE_STRING,
        "coAccountLabel" => self::TYPE_STRING,
        "adminNumber" => self::TYPE_STRING,
        "instituteNumber" => self::TYPE_BOOLEAN
    ];
}
