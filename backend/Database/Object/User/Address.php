<?php

namespace Database\Object\User;

use Database\Interface\CustomObject;

class Address extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "userId" => self::TYPE_INTEGER,
        "informatEmployeeAddressId" => self::TYPE_STRING,
        "street" => self::TYPE_STRING,
        "number" => self::TYPE_INTEGER,
        "bus" => self::TYPE_STRING,
        "zipcode" => self::TYPE_STRING,
        "city" => self::TYPE_STRING,
        "countryId" => self::TYPE_INTEGER,
        "current" => self::TYPE_BOOLEAN,
        "since" => self::TYPE_DATETIME,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        // "user" => [
        //     "userId" => \Database\Repository\User\User::class
        // ],
        "country" => [
            "countryId" => \Database\Repository\General\Country::class
        ]
    ];

    public function init()
    {
        $this->formatted->addressHash = "{$this->street}{$this->number}{$this->bus}{$this->zipcode}{$this->city}{$this->countryId}";
        $this->formatted->address = "{$this->street} {$this->number}" . ($this->bus ? "/{$this->bus}" : "") . ", {$this->zipcode} {$this->city} {$this->linked->country->translatedName}";
    }
}
