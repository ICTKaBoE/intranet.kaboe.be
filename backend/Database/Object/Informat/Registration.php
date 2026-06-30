<?php

namespace Database\Object\Informat;

use Ouzo\Utilities\Clock;
use Security\CustomObject;

class Registration extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatId" => self::TYPE_INTEGER,
        "informatGuid" => self::TYPE_STRING,
        "informatStudentId" => self::TYPE_INTEGER,
        "schoolInstituteId" => self::TYPE_INTEGER,
        "basenumber" => self::TYPE_STRING,
        "departmentCode" => self::TYPE_STRING,
        "departmentName" => self::TYPE_STRING,
        "grade" => self::TYPE_INTEGER,
        "year" => self::TYPE_INTEGER,
        "start" => self::TYPE_DATE,
        "virtualStart" => self::TYPE_DATE,
        "end" => self::TYPE_DATE,
        "virtualEnd" => self::TYPE_DATE,
        "status" => self::TYPE_INTEGER,
        "current" => self::TYPE_BOOLEAN
    ];

    public function init()
    {
        $this->actualYear = ($this->grade - 1) * 2 + $this->year;
        $this->formatted->dates = Clock::at($this->start)->format("d/m/Y") . (is_null($this->end) ? "" : " - " . Clock::at($this->end)->format("d/m/Y"));
    }
}
