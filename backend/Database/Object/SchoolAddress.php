<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\School;

class SchoolAddress extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "schoolId" => "int",
        "street" => "string",
        "number" => "int",
        "bus" => "string",
        "zipcode" => "string",
        "city" => "string",
        "country" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School::class]
    ];

    public function init()
    {
        $this->formatted->address = "{$this->street} {$this->number}{$this->bus}, {$this->zipcode} {$this->city}";
        $this->formatted->addressWithSchool = "{$this->linked->school->name} - {$this->formatted->address}";
    }
}
