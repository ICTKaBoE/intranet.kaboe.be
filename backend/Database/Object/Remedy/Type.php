<?php

namespace Database\Object\Remedy;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Security\CustomObject;
use stdClass;

class Type extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "departmentId" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "color" => self::TYPE_STRING,
        "from" => self::TYPE_DATE,
        "until" => self::TYPE_DATE,
        "courseDependsOnSkore" => self::TYPE_BOOLEAN,
        "manualAssignDate" => self::TYPE_BOOLEAN,
        "onComputer" => self::TYPE_BOOLEAN,
        "allowMultipleRegistrationsForThisTypeOnSameDay" => self::TYPE_BOOLEAN,
        "allowMultipleRegistrationsForThisTypeOnSameDayAmount" => self::TYPE_INTEGER,
        "allowMultipleRegistrationsForSameCourseOnSameDay" => self::TYPE_BOOLEAN,
        "allowMultipleRegistrationsForSameCourseOnSameDayAmount" => self::TYPE_INTEGER,
        "allowWithoutDate" => self::TYPE_BOOLEAN,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "department" => ["departmentId" => \Database\Repository\School\Department::class]
    ];

    public function init()
    {
        if ($this->from < 0) $this->from = null;
        if ($this->until < 0) $this->until = null;

        $this->formatted->from = new stdClass;
        $this->formatted->until = new stdClass;

        if ($this->from) {
            $this->formatted->from->display = Clock::at($this->from)->format("d/m/Y");
            $this->formatted->from->sort = Clock::at($this->from)->format("U");
        }

        if ($this->until) {
            $this->formatted->until->display = Clock::at($this->until)->format("d/m/Y");
            $this->formatted->until->sort = Clock::at($this->until)->format("U");
        }

        $this->formatted->department = $this->linked->department ? (is_array($this->linked->department) ? implode(", ", Arrays::map($this->linked->department, fn($c) => $c->name)) : $this->linked->department->name) : null;
    }
}
