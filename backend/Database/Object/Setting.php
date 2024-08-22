<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Setting extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "settingTabId" => "int",
        "name" => "string",
        "type" => "string",
        "options" => "list",
        "value" => "string",
        "order" => "int",
        "deleted" => "boolean"
    ];
}
