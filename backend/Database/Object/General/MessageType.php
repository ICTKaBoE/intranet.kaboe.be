<?php

namespace Database\Object\General;

use Database\Interface\CustomObject;

class MessageType extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
    ];
}
