<?php

namespace Database\Object\HR;

use Security\CustomObject;

class Role extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "code" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN,
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School\School::class]
    ];

    public function init()
    {
        $this->formatted->nameWithSchool = "{$this->linked->school->name} - {$this->name}";
    }
}
