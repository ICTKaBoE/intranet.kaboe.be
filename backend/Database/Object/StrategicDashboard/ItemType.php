<?php

namespace Database\Object\StrategicDashboard;

use Ouzo\Utilities\Strings;
use Security\CustomObject;

class ItemType extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "short" => self::TYPE_STRING,
        "name" => self::TYPE_STRING
    ];
}
