<?php

namespace Smartschool\Object;

use Security\CustomObject;

class Courses extends CustomObject
{
    protected $objectAttributes = [
        "name" => self::TYPE_STRING,
        "description" => self::TYPE_STRING,
        "active" => self::TYPE_BOOLEAN,
        "mainTeacher" => self::TYPE_OBJECT,
        "coTeachers" => [
            "type" => self::TYPE_ALL,
            "sub" => [
                "coTeacher" => self::TYPE_ARRAY_OR_OBJECT
            ]
        ],
        "studentGroups" => [
            "type" => self::TYPE_ALL,
            "sub" => [
                "studentGroup" => self::TYPE_ARRAY
            ]
        ]
    ];
}
