<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class SettingTab extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
        "icon" => "string",
        "order" => "int",
        "deleted" => "boolean"
    ];
}
