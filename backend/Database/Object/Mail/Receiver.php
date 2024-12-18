<?php

namespace Database\Object\Mail;

use Database\Interface\CustomObject;

class Receiver extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "mailId" => "int",
        "email" => "string",
        "name" => "string",
        "deleted" => "boolean"
    ];
}
