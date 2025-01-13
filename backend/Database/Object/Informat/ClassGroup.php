<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

class ClassGroup extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatId" => "int",
        "informatGuid" => "string",
        "schoolInstituteId" => "int",
        "schoolyear" => "string",
        "code" => "string",
        "name" => "string",
        "type" => "string"
    ];
}
