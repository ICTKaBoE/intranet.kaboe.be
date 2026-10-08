<?php

namespace Database\Object\Informat;

use Security\CustomObject;

class StudentConfig extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "schoolyearId" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "registrationId" => self::TYPE_INTEGER,
        "classgroupId" => self::TYPE_INTEGER,
        "active" => self::TYPE_BOOLEAN,
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "informatStudent" => ["informatStudentId" => \Database\Repository\Informat\Student::class],
        "classgroup" => ["classgroupId" => \Database\Repository\Informat\ClassGroup::class],
        "registration" => ["registrationId" => \Database\Repository\Informat\Registration::class]
    ];
}
