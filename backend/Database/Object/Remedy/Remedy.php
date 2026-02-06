<?php

namespace Database\Object\Remedy;

use Helpers\HTML;
use stdClass;
use Ouzo\Utilities\Clock;
use Security\CustomObject;

class Remedy extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "creatorUserId" => self::TYPE_INTEGER,
        "typeId" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "departmentId" => self::TYPE_INTEGER,
        "courseId" => self::TYPE_INTEGER,
        "momentId" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "classgroupId" => self::TYPE_INTEGER,
        "remark" => self::TYPE_STRING,
        "assignedDate" => self::TYPE_DATE,
        "present" => self::TYPE_BOOLEAN,
        "computerType" => self::TYPE_STRING,
        "computerTypeOther" => self::TYPE_STRING,
        "computerPassword" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "creatorUser" => ["creatorUserId" => \Database\Repository\User\User::class],
        "type" => ["typeId" => \Database\Repository\Remedy\Type::class],
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "department" => ["departmentId" => \Database\Repository\School\Department::class],
        "course" => ["courseId" => \Database\Repository\School\Course::class],
        "moment" => ["momentId" => \Database\Repository\Remedy\Moment::class],
        "informatStudent" => ["informatStudentId" => \Database\Repository\Informat\Student::class],
        "classgroup" => ["classgroupId" => \Database\Repository\Informat\ClassGroup::class],
        "computerType" => ["computerType" => \Database\Repository\Remedy\ComputerType::class]
    ];

    public function init()
    {
        $this->formatted->date = new stdClass;
        $this->formatted->status = is_null($this->present) ? "" : ($this->present ? "Aanwezig" : "Afwezig");
        $this->formatted->badge->status = is_null($this->present) ? "" : HTML::Badge($this->present ? "Aanwezig" : "Afwezig", backgroundColor: $this->present ? "green" : "red");

        if ($this->linked->type->manualAssignDate && $this->assignedDate) {
            $this->formatted->date->display = Clock::at($this->assignedDate)->format("d/m/Y");
            $this->formatted->date->sort = Clock::at($this->assignedDate)->format("U");
        } else if (!$this->assignedDate) {
            $this->formatted->date->display = $this->linked->moment->formatted->date->display . ($this->linked->type->manualAssignDate ? " (N.T.B.)" : "");
            $this->formatted->date->sort = $this->linked->moment->formatted->date->sort;
        }
    }
}
