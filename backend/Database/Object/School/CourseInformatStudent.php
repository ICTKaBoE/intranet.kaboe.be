<?php

namespace Database\Object\School;

use Security\CustomObject;

class CourseInformatStudent extends CustomObject
{
    protected $objectAttributes = [
        "schoolCourseId" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER
    ];
}
