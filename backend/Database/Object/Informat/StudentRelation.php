<?php

namespace Database\Object\Informat;

use Security\Input;
use Database\Interface\CustomObject;

class StudentRelation extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatStudentId" => "int",
        "informatId" => "int",
        "informatGuid" => "string",
        "type" => "string",
        "name" => "string",
        "firstName" => "string",
        "insz" => "string",
        "birthDate" => "date",
        "sex" => "string",
        "nationalityId" => "int",
        "job" => "string",
        "civilStatus" => "string",
        "rank" => "int"
    ];

    public function init()
    {
        $this->formatted->fullName = Input::createDisplayName("{{FN}} {{LN}}", $this->firstName, $this->name);
        $this->formatted->fullNameReversed = Input::createDisplayName("{{LN}} {{FN}}", $this->firstName, $this->name);
        $this->formatted->typeWithFullName = "{$this->type}:\t{$this->formatted->fullName}";
        $this->formatted->typeWithFullNameReversed = "{$this->type}:\t{$this->formatted->fullNameReversed}";
    }
}
