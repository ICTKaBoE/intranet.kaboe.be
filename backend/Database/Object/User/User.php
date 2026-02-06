<?php

namespace Database\Object\User;

use Security\CustomObject;
use Helpers\CString;
use Security\Input;

class User extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "entraId" => self::TYPE_STRING,
        "entraCompany" => self::TYPE_STRING,
        "informatEmployeeId" => self::TYPE_STRING,
        "mainSchoolId" => self::TYPE_INTEGER,
        "username" => self::TYPE_STRING,
        "password" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "firstName" => self::TYPE_STRING,
        "bankAccount" => self::TYPE_STRING,
        "active" => self::TYPE_BOOLEAN,
        "api" => self::TYPE_BOOLEAN,
        "system" => self::TYPE_BOOLEAN,
        "deleted" => self::TYPE_BOOLEAN
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
