<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;

class Field extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "schoolyearId" => "int",
        "studyyearId" => "int",
        "name" => "string",
        "administrativeGroupNumber" => "string",
        "maxCapacity" => "int",
        "capacityType" => "string",
        "full" => "boolean",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School\School::class],
        "schoolyear" => ["schoolyearId" => \Database\Repository\Registration\Schoolyear::class],
        "studyyear" => ["studyyearId" => \Database\Repository\Registration\Studyyear::class]
    ];
}
