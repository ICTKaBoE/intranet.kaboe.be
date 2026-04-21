<?php

namespace Informat\Object;

use Security\CustomObject;

class Employee extends CustomObject
{
    protected $objectAttributes = [
        "pPersoon" => self::TYPE_INTEGER,
        "personId" => self::TYPE_STRING,
        "personaId" => self::TYPE_STRING,
        "naam" => self::TYPE_STRING,
        "voornaam" => self::TYPE_STRING,
        "bijkomendeVoornamen" => self::TYPE_STRING,
        "stamnr" => self::TYPE_STRING,
        "geslacht" => self::TYPE_STRING,
        "geboortedatum" => self::TYPE_DATE,
        "geboorteplaats" => self::TYPE_STRING,
        "geboortelandCode" => self::TYPE_STRING,
        "nationaliteitCode" => self::TYPE_STRING,
        "rijksregisternr" => self::TYPE_STRING,
        "bisnr" => self::TYPE_STRING,
        "bank" => self::TYPE_OBJECT,
        "hoofdAmbt" => self::TYPE_OBJECT,
        "eersteDienstSchool" => self::TYPE_DATE,
        "eersteDienstScholengroep" => self::TYPE_DATE,
        "eersteDienstScholengemeenschap" => self::TYPE_DATE,
        "isActive" => self::TYPE_BOOLEAN,
        "pensioendatum" => self::TYPE_DATE,
        "isMindervalide" => self::TYPE_BOOLEAN,
        "isOverleden" => self::TYPE_BOOLEAN,
        "partner" => self::TYPE_OBJECT,
        "adressen" => self::TYPE_ARRAY,
        "comnrs" => self::TYPE_ARRAY,
        "emailadressen" => self::TYPE_ARRAY,
        "kinderen" => self::TYPE_ARRAY,
        "personeelsgroepen" => self::TYPE_ARRAY
    ];
}
