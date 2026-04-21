<?php

namespace Informat\Object;

use Security\CustomObject;

class Registration extends CustomObject
{
    protected $objectAttributes = [
        "inschrijvingsId" => self::TYPE_STRING,
        "pInschr" => self::TYPE_INTEGER,
        "pPersoon" => self::TYPE_INTEGER,
        "persoonId" => self::TYPE_STRING,
        "instelnr" => self::TYPE_STRING,
        "hfdstructuur" => self::TYPE_STRING,
        "school" => self::TYPE_STRING,
        "stamnr" => self::TYPE_STRING,
        "vestCode" => self::TYPE_STRING,
        "vestiging" => self::TYPE_STRING,
        "begindatum" => "date",
        "einddatum" => "date",
        "afdCode" => self::TYPE_STRING,
        "nrAdmgrp" => self::TYPE_STRING,
        "afdelingsjaar" => self::TYPE_STRING,
        "status" => self::TYPE_INTEGER,
        "graad" => self::TYPE_STRING,
        "leerjaar" => self::TYPE_INTEGER,
        "taalkeuze" => self::TYPE_STRING,
        "finCode" => self::TYPE_STRING,
        "levensbeschouwingCode" => self::TYPE_STRING,
        "isOkan" => self::TYPE_BOOLEAN,
        "preRegistrationId" => self::TYPE_STRING,
        "inschrKlassen" => self::TYPE_ARRAY
    ];
}
