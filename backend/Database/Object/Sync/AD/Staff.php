<?php

namespace Database\Object\Sync\AD;

use Database\Interface\CustomObject;

class Staff extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "action" => "string",
        "displayName" => "string",
        "firstName" => "string",
        "name" => "string",
        "email" => "string",
        "password" => "string",
        "companyName" => "string",
        "department" => "string",
        "jobTitle" => "string",
        "employeeId" => "int",
        "ou" => "string",
        "memberOf" => "string",
        "samAccountName" => "string"
    ];
}
