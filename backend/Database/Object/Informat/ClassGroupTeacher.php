<?php

namespace Database\Object\Informat;

use Security\CustomObject;

class ClassgroupTeacher extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatClassgroupId" => self::TYPE_INTEGER,
        "informatEmployeeId" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "informatEmployee" => ['informatEmployeeId' => \Database\Repository\Informat\Employee::class]
    ];
}
