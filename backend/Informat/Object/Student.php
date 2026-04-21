<?php

namespace Informat\Object;

use Security\CustomObject;

class Student extends CustomObject
{
    protected $objectAttributes = [
        "pPersoon" => self::TYPE_INTEGER,
        "persoonId" => self::TYPE_STRING,
        "naam" => self::TYPE_STRING,
        "voornaam" => self::TYPE_STRING,
        "geboortedatum" => self::TYPE_DATE,
        "nickname" => self::TYPE_STRING,
        "voornaam2" => self::TYPE_STRING,
        "initialen" => self::TYPE_STRING,
        "geboorteland" => self::TYPE_STRING,
        "geboorteplaats" => self::TYPE_STRING,
        "nationaliteitCode" => self::TYPE_STRING,
        "rijksregisternr" => self::TYPE_STRING,
        "bisnr" => self::TYPE_STRING,
        "geslacht" => self::TYPE_STRING,
        "huisdokter" => self::TYPE_STRING,
        "telefoonHuisdokter" => self::TYPE_STRING,
        "llOpSchool" => self::TYPE_INTEGER,
        "inschrijvingsId" => self::TYPE_STRING,
        "leerlingenkaartNummer" => self::TYPE_STRING,
        "fietsNummer" => self::TYPE_STRING,
        "adressen" => self::TYPE_ARRAY,
        "overigeAdressen" => self::TYPE_ARRAY,
        "relaties" => self::TYPE_ARRAY,
        "comnrs" => self::TYPE_ARRAY,
        "emails" => self::TYPE_ARRAY,
        "bankrek" => self::TYPE_ARRAY
    ];
}
