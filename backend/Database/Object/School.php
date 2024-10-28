<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class School extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "name" => "string",
        "color" => "string",
        "intuneOrderIdPrefix" => "string",
        "jamfIpadPrefix" => "string",
        "deleted" => "boolean"
    ];

    public function init()
    {
        $this->formatted->badge->name = "<span class=\"badge text-white\" style=\"margin-top: 2px; background-color: {$this->color}\">{$this->name}</span>";
    }
}
