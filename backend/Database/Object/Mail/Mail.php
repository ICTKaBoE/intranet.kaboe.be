<?php

namespace Database\Object\Mail;

use Database\Interface\CustomObject;

class Mail extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "subject" => "string",
        "body" => "string",
        "html" => "boolean",
        "replyTo" => "json",
        "sendAfterDateTime" => "datetime",
        "sentDateTime" => "datetime",
        "error" => "string",
        "deleted" => "boolean"
    ];
}
