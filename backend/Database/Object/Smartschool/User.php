<?php

namespace Database\Object\Smartschool;

use Security\CustomObject;

class User extends CustomObject
{
    const STATUS = [
        0 => "Uitgeschakeld",
        1 => "Actief",
        2 => "Administratief"
    ];

    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "internalId" => self::TYPE_INTEGER,
        "status" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "firstName" => self::TYPE_STRING,
        "username" => self::TYPE_STRING,
        "rank" => self::TYPE_INTEGER,
        "scannableCode" => self::TYPE_STRING,
        "lastLogin" => self::TYPE_DATETIME
    ];
}
