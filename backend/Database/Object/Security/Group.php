<?php

namespace Database\Object\Security;

use Security\CustomObject;
use Helpers\HTML;

class Group extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "priority" => self::TYPE_INTEGER,
        "m365GroupId" => self::TYPE_GUID,
        "deleted" => self::TYPE_BOOLEAN,
    ];
}
