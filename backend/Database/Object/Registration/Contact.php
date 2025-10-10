<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;

class Contact extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "registrationId" => self::TYPE_INTEGER,
        "followNumber" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "firstName" => self::TYPE_STRING,
        "relation" => self::TYPE_STRING,
        "relationOther" => self::TYPE_STRING,
        "phonePrivate" => self::TYPE_STRING,
        "phoneWork" => self::TYPE_STRING,
        "phoneMobile" => self::TYPE_STRING,
        "email" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
