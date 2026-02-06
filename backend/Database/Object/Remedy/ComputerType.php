<?php

namespace Database\Object\Remedy;

use Ouzo\Utilities\Clock;
use Security\CustomObject;
use stdClass;

class ComputerType extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
