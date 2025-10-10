<?php

namespace Database\Object\General;

use Database\Interface\CustomObject;

class Country extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "alpha2Code" => self::TYPE_STRING,
        "alpha3Code" => self::TYPE_STRING,
        "cioc" => self::TYPE_STRING,
        "numericCode" => self::TYPE_STRING,
        "callingCode" => self::TYPE_STRING,
        "officialName" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "nisCode" => self::TYPE_INTEGER,
    ];

    public function init()
    {
        $this->fullNisCode = str_pad($this->nisCode, (5 - strlen($this->nisCode)), 0, STR_PAD_LEFT);
    }
}
