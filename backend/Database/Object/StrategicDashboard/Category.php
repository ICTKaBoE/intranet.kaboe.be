<?php

namespace Database\Object\StrategicDashboard;

use Security\CustomObject;

class Category extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "name" => self::TYPE_STRING,
        "image" => self::TYPE_BOOLEAN,
        "order" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
