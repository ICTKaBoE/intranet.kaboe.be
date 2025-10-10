<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

class ClassGroup extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatId" => self::TYPE_INTEGER,
        "informatGuid" => self::TYPE_STRING,
        "schoolInstituteId" => self::TYPE_INTEGER,
        "schoolyear" => self::TYPE_STRING,
        "administrativeGroupCode" => self::TYPE_STRING,
        "departmentCode" => self::TYPE_STRING,
        "grade" => self::TYPE_INTEGER,
        "year" => self::TYPE_INTEGER,
        "code" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "type" => self::TYPE_STRING
    ];

    protected $linkedAttributes = [
        "schoolInstitute" => [
            "schoolInstituteId" => \Database\Repository\School\Institute::class
        ]
    ];
}
