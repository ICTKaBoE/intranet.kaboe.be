<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;

class Field extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "schoolyearId" => self::TYPE_INTEGER,
        "studyyearId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "administrativeGroupNumber" => self::TYPE_STRING,
        "maxCapacity" => self::TYPE_INTEGER,
        "capacityType" => self::TYPE_STRING,
        "full" => self::TYPE_BOOLEAN,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School\School::class],
        "schoolyear" => ["schoolyearId" => \Database\Repository\Registration\Schoolyear::class],
        "studyyear" => ["studyyearId" => \Database\Repository\Registration\Studyyear::class]
    ];
}
