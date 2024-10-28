<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

class Teacher extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatId" => "int",
        "basenumber" => "string",
        "name" => "string",
        "firstName" => "string",
        "schoolyear" => "string",
        "homePhone" => "string",
        "mobilePhone" => "string",
        "email" => "string",
        "street" => "string",
        "number" => "string",
        "bus" => "string",
        "zipcode" => "string",
        "city" => "string",
        "bankAccount" => "string",
        "active" => "boolean",
        "countryCode" => "string"
    ];
}
