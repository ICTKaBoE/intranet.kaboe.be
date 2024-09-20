<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\CString;

class User extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "entraId" => "string",
        "informatEmployeeId" => "int",
        "mainSchoolId" => "int",
        "username" => "string",
        "password" => "string",
        "name" => "string",
        "firstName" => "string",
        "bankAccount" => "string",
        "active" => "boolean",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "mainSchool" => [
            "mainSchoolId" => \Database\Repository\School::class
        ]
    ];

    public function init()
    {
        $this->fullName = "{$this->firstName} {$this->name}";
        $this->fullNameReversed = "{$this->name} {$this->firstName}";
        $this->initials = CString::firstLetterOfEachWord($this->fullName);

        $this->formatted->fullName = $this->fullName;
        $this->formatted->fullNameReversed = $this->fullNameReversed;
        $this->formatted->initials = $this->initials;
    }
}
