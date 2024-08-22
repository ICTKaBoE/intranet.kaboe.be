<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Strings;

class InformatEmployee extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatId" => "int",
        "personId" => "string",
        "name" => "string",
        "firstName" => "string",
        "extraFirstName" => "string",
        "baseNumber" => "string",
        "sex" => "string",
        "birthdate" => "date",
        "birthplace" => "string",
        "birthCountryNISCode" => "int",
        "nationalityNISCode" => "int",
        "nin" => "string",
        "bis" => "string",
        "bankAccount" => "string",
        "bankBic" => "string",
        "active" => "bool"
    ];

    public function init()
    {
        $this->sex = (Strings::equal($this->sex, "M") ? "M" : (Strings::equal($this->sex, "V") ? "F" : "X"));
    }
}
