<?php

namespace Database\Object\General;

use Database\Interface\CustomObject;

class Country extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "alpha2Code" => "string",
        "alpha3Code" => "string",
        "cioc" => "string",
        "numericCode" => "string",
        "callingCode" => "string",
        "officialName" => "string",
        "name" => "string",
        "nisCode" => "int",
    ];

    public function init()
    {
        $this->fullNisCode = str_pad($this->nisCode, (5 - strlen($this->nisCode)), 0, STR_PAD_LEFT);
    }
}
