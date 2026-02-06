<?php

namespace Database\Object\Navigation;

use Security\CustomObject;

class TableDef  extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "navigationId" => self::TYPE_INTEGER,
        "order" => self::TYPE_INTEGER,
        "priority" => self::TYPE_INTEGER,
        "type" => self::TYPE_STRING,
        "title" => self::TYPE_STRING,
        "data" => self::TYPE_STRING,
        "orderable" => self::TYPE_BOOLEAN,
        "searchable" => self::TYPE_BOOLEAN,
        "width" => self::TYPE_INTEGER,
        "render" => self::TYPE_BOOLEAN,
        "defaultOrder" => self::TYPE_BOOLEAN,
        "defaultOrderOrder" => self::TYPE_INTEGER,
        "defaultOrderDirection" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN,
    ];
}
