<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class Employee extends CustomObject
{
    protected $objectAttributes = [
        "pPersoon" => "int",
        "personId" => "string",
        "naam" => "string",
        "voornaam" => "string",
        "bijkomendeVoornamen" => "string",
        "stamnr" => "string",
        "geslacht" => "string",
        "geboortedatum" => "date",
        "geboorteplaats" => "string",
        "geboortelandCode" => "string",
        "nationaliteitCode" => "string",
        "rijksregisternr" => "string",
        "bisnr" => "string",
        "bank" => "object",
        "isActive" => "bool",
        "adressen" => "array",
        "comnrs" => "array",
        "emailaddress" => "array"
    ];
}
