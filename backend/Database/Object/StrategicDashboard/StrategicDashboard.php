<?php

namespace Database\Object\StrategicDashboard;

use Security\CustomObject;

class StrategicDashboard extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "datetime" => self::TYPE_DATETIME,
        "val1" => self::TYPE_INTEGER,
        "val2" => self::TYPE_INTEGER,
        "val3" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
