<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class Student extends CustomObject
{
    protected $objectAttributes = [
        "pPersoon" => "int",
        "persoonId" => "string",
        "naam" => "string",
        "voornaam" => "string",
        "geboortedatum" => "date",
        "nickname" => "string",
        "voornaam2" => "string",
        "initialen" => "string",
        "geboorteland" => "string",
        "geboorteplaats" => "string",
        "nationaliteitCode" => "string",
        "rijksregisternr" => "string",
        "bisnr" => "string",
        "geslacht" => "string",
        "huisdokter" => "string",
        "telefoonHuisdokter" => "string",
        "llOpSchool" => "int",
        "inschrijvingsId" => "string",
        "leerlingenkaartNummer" => "string",
        "fietsNummer" => "string",
        "adressen" => "array",
        "relaties" => "array",
        "comnrs" => "array",
        "emails" => "array",
        "bankrek" => "array"
    ];
}
