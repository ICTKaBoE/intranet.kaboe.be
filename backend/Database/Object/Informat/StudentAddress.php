<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;
use Helpers\CString;

class StudentAddress extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatStudentId" => "int",
        "informatId" => "int",
        "informatGuid" => "string",
        "street" => "string",
        "number" => "int",
        "bus" => "string",
        "zipcode" => "string",
        "city" => "string",
        "countryId" => "int"
    ];

    protected $linkedAttributes = [
        "country" => ["countryId" => \Database\Repository\Country::class]
    ];

    public function init()
    {
        $this->formatted->full = CString::formatAddress($this->street, $this->number, $this->bus, $this->zipcode, $this->city, $this->linked->country->translatedName);
    }
}
