<?php

namespace Database\Object;

use Router\Helpers;
use Security\Session;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;
use stdClass;

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
        "translatedName" => "string",
        "nisCode" => "int"
    ];

    public function init()
    {
        $this->fullNisCode = str_pad($this->nisCode, (5 - strlen($this->nisCode)), 0, STR_PAD_LEFT);
    }
}
