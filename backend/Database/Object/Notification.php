<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Notification extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "userId" => "int",
        "showtime" => "datetime",
        "type" => "string",
        "link" => "string",
        "delay" => "int",
        "message" => "string",
        "deleted" => "bool"
    ];
}
