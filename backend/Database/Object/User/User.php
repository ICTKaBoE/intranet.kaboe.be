<?php

namespace Database\Object\User;

use Database\Interface\CustomObject;
use Helpers\CString;
use Security\Input;

class User extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "entraId" => "string",
        "informatEmployeeId" => "string",
        "mainSchoolId" => "int",
        "username" => "string",
        "password" => "string",
        "name" => "string",
        "firstName" => "string",
        "bankAccount" => "string",
        "active" => "boolean",
        "api" => "boolean",
        "system" => "boolean",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "mainSchool" => [
            "mainSchoolId" => \Database\Repository\School\School::class
        ]
    ];

    public function init()
    {
        $this->fullName = Input::createDisplayName("{{FN}} {{LN}}", $this->firstName, $this->name);
        $this->fullNameReversed = Input::createDisplayName("{{LN}} {{FN}}", $this->firstName, $this->name);
        $this->initials = CString::firstLetterOfEachWord($this->fullName);

        $this->formatted->fullName = $this->fullName;
        $this->formatted->fullNameReversed = $this->fullNameReversed;
        $this->formatted->initials = $this->initials;
    }
}
