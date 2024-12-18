<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

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
        $this->formatted->full = "{$this->street} {$this->number}" . ($this->bus ? "/{$this->bus}" : "") . ", {$this->zipcode} {$this->city} {$this->linked->country->translatedName}";
    }
}
