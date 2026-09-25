<?php

namespace Database\Object\Informat;

use Security\CustomObject;

class StudentConfig extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "schoolyearId" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "registrationId" => self::TYPE_INTEGER,
        "classgroupId" => self::TYPE_INTEGER,
        "active" => self::TYPE_BOOLEAN,
    ];

    protected $linkedAttributes = [
        "schoolyear" => ["schoolyearId" => \Database\Repository\General\Schoolyear::class],
        "informatStudent" => ["informatStudentId" => \Database\Repository\Informat\Student::class],
        "registration" => ["registrationId" => \Database\Repository\Informat\Registration::class],
        "classgroup" => ["classgroupId" => \Database\Repository\Informat\ClassGroup::class]
    ];
}
