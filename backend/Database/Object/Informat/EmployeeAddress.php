<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;
use Helpers\CString;

class EmployeeAddress extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatEmployeeId" => "int",
        "informatGuid" => "string",
        "street" => "string",
        "number" => "int",
        "bus" => "string",
        "zipcode" => "string",
        "city" => "string",
        "countryId" => "int",
        "current" => "bool"
    ];

    protected $linkedAttributes = [
        "country" => [
            "countryId" => \Database\Repository\Country::class
        ]
    ];

    public function init()
    {
        $this->formatted->address = CString::formatAddress($this->street, $this->number, $this->bus, $this->zipcode, $this->city, $this->linked->country->translatedName) . ($this->current ? " (huidig)" : "");
    }
}
