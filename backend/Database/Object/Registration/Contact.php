<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;

class Contact extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "registrationId" => "int",
        "followNumber" => "int",
        "name" => "string",
        "firstName" => "string",
        "relation" => "string",
        "relationOther" => "string",
        "phonePrivate" => "string",
        "phoneWork" => "string",
        "phoneMobile" => "string",
        "email" => "string",
        "deleted" => "boolean"
    ];
}
