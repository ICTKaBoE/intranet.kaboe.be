<?php

namespace Database\Object\Registration;

use Security\CustomObject;

class Option extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "schoolyearId" => self::TYPE_INTEGER,
        "studyyearId" => self::TYPE_INTEGER,
        "fieldId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School\School::class],
        "schoolyear" => ["schoolyearId" => \Database\Repository\General\Schoolyear::class],
        "studyyear" => ["studyyearId" => \Database\Repository\Registration\Studyyear::class],
        "field" => ["fieldId" => \Database\Repository\Registration\Field::class]
    ];
}
