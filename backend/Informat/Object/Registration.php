<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class Registration extends CustomObject
{
    protected $objectAttributes = [
        "inschrijvingsId" => "string",
        "pInschr" => "int",
        "pPersoon" => "int",
        "persoonId" => "string",
        "instelnr" => "string",
        "hfdstructuur" => "string",
        "school" => "string",
        "stamnr" => "string",
        "vestCode" => "string",
        "vestiging" => "string",
        "begindatum" => "date",
        "einddatum" => "date",
        "afdCode" => "string",
        "nrAdmgrp" => "string",
        "afdelingsjaar" => "string",
        "status" => "int",
        "graad" => "string",
        "leerjaar" => "int",
        "taalkeuze" => "string",
        "finCode" => "string",
        "levensbeschouwingCode" => "string",
        "isOkan" => "bool",
        "preRegistrationId" => "string",
        "inschrKlassen" => "array"
    ];
}
