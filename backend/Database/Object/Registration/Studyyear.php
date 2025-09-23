<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;

class Studyyear extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "schoolyearId" => "int",
        "name" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School\School::class],
        "schoolyear" => ["schoolyearId" => \Database\Repository\Registration\Schoolyear::class]
    ];
}
