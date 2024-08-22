<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\CString;

class User extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "entraId" => "string",
        "username" => "string",
        "password" => "string",
        "name" => "string",
        "firstName" => "string",
        "active" => "boolean",
        "deleted" => "boolean"
    ];

    public function init()
    {
        $this->fullName = "{$this->firstName} {$this->name}";
        $this->fullNameReversed = "{$this->name} {$this->firstName}";
        $this->initials = CString::firstLetterOfEachWord($this->fullName);
    }
}
