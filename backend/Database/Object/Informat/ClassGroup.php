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
        "departmentCode" => "string",
        "grade" => "int",
        "year" => "int",
        "code" => "string",
        "name" => "string",
        "type" => "string"
    ];

    protected $linkedAttributes = [
        "schoolInstitute" => [
            "schoolInstituteId" => \Database\Repository\SchoolInstitute::class
        ]
    ];
}
