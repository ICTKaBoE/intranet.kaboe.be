<?php

namespace Database\Object\Informat;

use Security\CustomObject;
use Helpers\CString;

class StudentAddress extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "informatId" => self::TYPE_INTEGER,
        "informatGuid" => self::TYPE_STRING,
        "street" => self::TYPE_STRING,
        "number" => self::TYPE_INTEGER,
        "bus" => self::TYPE_STRING,
        "zipcode" => self::TYPE_STRING,
        "city" => self::TYPE_STRING,
        "countryId" => self::TYPE_INTEGER,
        "domicile" => self::TYPE_BOOLEAN,
    ];

    protected $linkedAttributes = [
        "country" => ["countryId" => \Database\Repository\General\Country::class]
    ];

    public function init()
    {
        $this->formatted->full = CString::formatAddress($this->street, $this->number, $this->bus, $this->zipcode, $this->city, $this->linked->country->name);
        $this->formatted->doubleLine = CString::formatAddress($this->street, $this->number, $this->bus, $this->zipcode, $this->city, $this->linked->country->name, true);
        if ($this->domicile) $this->formatted->full .= " (domicilie)";
    }
}
