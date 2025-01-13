<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class Employee extends CustomObject
{
    protected $objectAttributes = [
        "pPersoon" => "int",
        "personId" => "string",
        "personaId" => "string",
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
        "hoofdAmbt" => "object",
        "eersteDienstSchool" => "date",
        "eersteDienstScholengroep" => "date",
        "eersteDienstScholengemeenschap" => "date",
        "isActive" => "bool",
        "pensioendatum" => "date",
        "isMindervalide" => "bool",
        "isOverleden" => "bool",
        "partner" => "object",
        "adressen" => "array",
        "comnrs" => "array",
        "emailadressen" => "array",
        "kinderen" => "array",
        "personeelsgroepen" => "array"
    ];
}
