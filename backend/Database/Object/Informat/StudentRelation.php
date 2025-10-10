<?php

namespace Database\Object\Informat;

use Security\Input;
use Database\Interface\CustomObject;

class StudentRelation extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "informatId" => self::TYPE_INTEGER,
        "informatGuid" => self::TYPE_STRING,
        "type" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "firstName" => self::TYPE_STRING,
        "insz" => self::TYPE_STRING,
        "birthDate" => self::TYPE_DATE,
        "sex" => self::TYPE_STRING,
        "nationalityId" => self::TYPE_INTEGER,
        "job" => self::TYPE_STRING,
        "civilStatus" => self::TYPE_STRING,
        "rank" => self::TYPE_INTEGER
    ];

    public function init()
    {
        $this->formatted->fullName = Input::createDisplayName("{{FN}} {{LN}}", $this->firstName, $this->name);
        $this->formatted->fullNameReversed = Input::createDisplayName("{{LN}} {{FN}}", $this->firstName, $this->name);
        $this->formatted->typeWithFullName = "{$this->type}:\t{$this->formatted->fullName}";
        $this->formatted->typeWithFullNameReversed = "{$this->type}:\t{$this->formatted->fullNameReversed}";
    }
}
