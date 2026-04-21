<?php

namespace Smartschool\Object;

use Security\CustomObject;

class AllAccountsExtended extends CustomObject
{
    protected $objectAttributes = [
        "voornaam" => self::TYPE_STRING,
        "naam" => self::TYPE_STRING,
        "gebruikersnaam" => self::TYPE_STRING,
        "internnummer" => self::TYPE_STRING,
        "status" => self::TYPE_STRING,
        "geboortedatum" => self::TYPE_DATE,
        "rijksregisternummer" => self::TYPE_STRING,
        "BadgeID" => self::TYPE_STRING,
        "Cateringtarief" => self::TYPE_STRING,
        "schoolverlater" => self::TYPE_BOOLEAN
    ];
}
