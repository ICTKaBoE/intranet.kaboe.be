<?php

namespace Database\Object\School;

use Security\CustomObject;

class CourseInformatEmployeeClassgroup extends CustomObject
{
    protected $objectAttributes = [
        "schoolCourseId" => self::TYPE_INTEGER,
        "informatEmployeeId" => self::TYPE_INTEGER,
        "informatClassgroupId" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "informatEmployee" => ["informatEmployeeId" => \Database\Repository\Informat\Employee::class],
    ];
}
