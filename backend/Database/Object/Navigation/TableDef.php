<?php

namespace Database\Object\Navigation;

use Database\Interface\CustomObject;

class TableDef  extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "navigationId" => "int",
        "order" => "int",
        "priority" => "int",
        "type" => "string",
        "title" => "string",
        "data" => "string",
        "orderable" => "bool",
        "searchable" => "bool",
        "width" => "int",
        "render" => "bool",
        "defaultOrder" => "bool",
        "defaultOrderOrder" => "int",
        "defaultOrderDirection" => "string",
        "deleted" => "bool",
    ];
}
