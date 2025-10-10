<?php

namespace Database\Object\Accident;

use Database\Interface\CustomObject;
use Database\Repository\Navigation\Navigation;
use Helpers\HTML;

class Document extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "alias" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "ext" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];
}
