<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

class TeacherFreefield extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatTeacherId" => "int",
        "description" => "string",
        "value" => "string",
        "section" => "string"
    ];
}
