<?php

namespace Database\Object\User;

use Database\Interface\CustomObject;

class Address extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "userId" => "int",
        "informatEmployeeAddressId" => "string",
        "street" => "string",
        "number" => "int",
        "bus" => "string",
        "zipcode" => "string",
        "city" => "string",
        "countryId" => "int",
        "current" => "boolean",
        "since" => "datetime",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "user" => [
            "userId" => \Database\Repository\User\User::class
        ],
        "country" => [
            "countryId" => \Database\Repository\Country::class
        ]
    ];

    public function init()
    {
        $this->formatted->addressHash = "{$this->street}{$this->number}{$this->bus}{$this->zipcode}{$this->city}{$this->countryId}";
        $this->formatted->address = "{$this->street} {$this->number}" . ($this->bus ? "/{$this->bus}" : "") . ", {$this->zipcode} {$this->city} {$this->linked->country->translatedName}";
    }
}
