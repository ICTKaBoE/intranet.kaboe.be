<?php

namespace Database\Object\School;

use Security\CustomObject;

class DepartmentClass extends CustomObject
{
    protected $objectAttributes = [
        "departmentId" => self::TYPE_INTEGER,
        "classgroupId" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "classgroup" => ["classgroupId" => \Database\Repository\Informat\ClassGroup::class]
    ];
}
