<?php

namespace Database\Object\School;

use Helpers\HTML;
use Helpers\CString;
use Security\CustomObject;

class School extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "virtual" => self::TYPE_BOOLEAN,
        "parentSchoolId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "color" => self::TYPE_STRING,
        "street" => self::TYPE_STRING,
        "number" => self::TYPE_INTEGER,
        "bus" => self::TYPE_STRING,
        "zipcode" => self::TYPE_STRING,
        "city" => self::TYPE_STRING,
        "countryId" => self::TYPE_INTEGER,
        "phone" => self::TYPE_STRING,
        "number" => self::TYPE_STRING,
        "import" => self::TYPE_BOOLEAN,
        "sync" => self::TYPE_BOOLEAN,
        "syncEmployeeCompanyName" => self::TYPE_STRING,
        "syncStudentCompanyName" => self::TYPE_STRING,
        "syncEmployeeOU" => self::TYPE_STRING,
        "syncStudentOU" => self::TYPE_STRING,
        "intuneOrderIdPrefix" => self::TYPE_STRING,
        "jamfIpadPrefix" => self::TYPE_STRING,
        "adJobTitlePrefix" => self::TYPE_STRING,
        "adOuPart" => self::TYPE_STRING,
        "adSecGroupPart" => self::TYPE_STRING,
        "syncUpdateMail" => self::TYPE_LIST,
        "dynamicTeam" => self::TYPE_BOOLEAN,
        "smartschoolSourceId" => self::TYPE_STRING,
        "eetjemeeKey" => self::TYPE_STRING,
        "eetjemeeSmartschoolGroup" => self::TYPE_STRING,
        "hrEmail" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "parentSchool" => ['parentSchoolId' => \Database\Repository\School\School::class],
        "country" => ["countryId" => \Database\Repository\General\Country::class]
    ];

    public function init()
    {
        $this->formatted->nameWithParent = ($this->linked->parentSchool ? $this->linked->parentSchool->name . " - " : "") . $this->name;
        $this->formatted->badge->name = HTML::Badge($this->name, style: [
            "margin-top" => "2px",
            "background-color" => $this->color
        ]);
        $this->formatted->badge->color = HTML::Badge("", style: [
            "padding" => "10px",
            "background-color" => $this->color
        ]);

        $this->formatted->icon->virtual = HTML::Icon($this->virtual ? "cloud" : "building");
        $this->formatted->icon->import = HTML::Icon($this->import ? "check" : "x", color: $this->import ? "green" : "red");
        $this->formatted->icon->sync = HTML::Icon($this->sync ? "check" : "x", color: $this->sync ? "green" : "red");

        $this->formatted->address = CString::formatAddress($this->street, $this->number, $this->bus, $this->zipcode, $this->city, $this->linked->country->name);
        $this->formatted->addressDoubleLine = CString::formatAddress($this->street, $this->number, $this->bus, $this->zipcode, $this->city, $this->linked->country->name, true);
        $this->formatted->addressWithSchool = "{$this->name} - {$this->formatted->address}";
    }
}
