<?php

namespace Database\Object\School;

use Database\Interface\CustomObject;

class Address extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "street" => self::TYPE_STRING,
        "number" => self::TYPE_INTEGER,
        "bus" => self::TYPE_STRING,
        "zipcode" => self::TYPE_STRING,
        "city" => self::TYPE_STRING,
        "country" => self::TYPE_STRING,
        "phone" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School\School::class]
    ];

    public function init()
    {
        $this->formatted->address = "{$this->street} {$this->number}{$this->bus}, {$this->zipcode} {$this->city}";
        $this->formatted->addressWithSchool = "{$this->linked->school->name} - {$this->formatted->address}";
    }
}
