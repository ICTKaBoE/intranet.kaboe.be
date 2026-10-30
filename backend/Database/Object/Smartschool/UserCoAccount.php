<?php

namespace Database\Object\Smartschool;

use Security\CustomObject;
use Security\Input;

class UserCoAccount extends CustomObject
{
    const STATUS = [
        0 => "Niet actief",
        1 => "Actief",
        2 => "Actief tot en met"
    ];

    const TYPE = [
        4 => "Interimaris",
        20 => "Stagiair",
        5 => "Collega",
        1 => "Moeder",
        2 => "Vader",
        28 => "Ouder",
        25 => "Plusmoeder",
        24 => "Plusvader",
        27 => "Meemoeder",
        26 => "Meevader",
        19 => "Stiefmoeder",
        18 => "Stiefvader",
        3 => "Voogd",
        7 => "Grootmoeder",
        6 => "Grootvader",
        8 => "Broer",
        9 => "Zus",
        21 => "Pleegmoeder",
        22 => "Pleegvader",
        12 => "ION-begeleider",
        23 => "Ondersteuner",
        13 => "Internaatsmedewerker",
        17 => "Studiecoach",
        10 => "Logopedist",
        16 => "Begeleider",
        14 => "Interne begeleider",
        15 => "Externe begeleider"
    ];

    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "userId" => self::TYPE_INTEGER,
        "index" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "firstName" => self::TYPE_STRING,
        "email" => self::TYPE_STRING,
        "emailVerified" => self::TYPE_BOOLEAN,
        "phone" => self::TYPE_STRING,
        "mobile" => self::TYPE_STRING,
        "type" => self::TYPE_INTEGER,
        "authenticatorApp" => self::TYPE_BOOLEAN,
        "yubikey" => self::TYPE_BOOLEAN,
        "status" => self::TYPE_INTEGER,
        "responsible" => self::TYPE_BOOLEAN,
        "lastLogin" => self::TYPE_DATETIME
    ];

    public function init()
    {
        $this->formatted->fullName = Input::createDisplayName("{{FN}} {{LN}}", $this->firstName, $this->name);
        $this->formatted->fullNameReversed = Input::createDisplayName("{{LN}} {{FN}}", $this->firstName, $this->name);
        $this->mapped->status = self::STATUS[$this->status];
        $this->mapped->type = self::TYPE[$this->type];
    }
}
