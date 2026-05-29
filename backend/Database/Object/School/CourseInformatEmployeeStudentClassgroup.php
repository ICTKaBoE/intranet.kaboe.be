<?php

namespace Database\Object\School;

use Security\CustomObject;

class CourseInformatEmployeeStudentClassgroup extends CustomObject
{
    protected $objectAttributes = [
        "schoolCourseId" => self::TYPE_INTEGER,
        "informatEmployeeId" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "informatClassgroupId" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "schoolCourse" => ["schoolCourseId" => \Database\Repository\School\Course::class],
        "informatStudent" => ["informatStudentId" => \Database\Repository\Informat\Student::class],
        "informatEmployee" => ["informatEmployeeId" => \Database\Repository\Informat\Employee::class],
    ];
}
