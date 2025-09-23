<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;

class Option extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "schoolyearId" => "int",
        "studyyearId" => "int",
        "fieldId" => "int",
        "name" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School\School::class],
        "schoolyear" => ["schoolyearId" => \Database\Repository\Registration\Schoolyear::class],
        "studyyear" => ["studyyearId" => \Database\Repository\Registration\Studyyear::class],
        "field" => ["fieldId" => \Database\Repository\Registration\Field::class]
    ];
}
