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
        "warnPasswordExpiration" => self::TYPE_BOOLEAN,
        "import" => self::TYPE_BOOLEAN,
        "syncEmployee" => self::TYPE_BOOLEAN,
        "syncEmployeeCompanyName" => self::TYPE_STRING,
        "syncEmployeeOU" => self::TYPE_STRING,
        "syncEmployeeDefaultMemberOf" => self::TYPE_STRING,
        "syncStudent" => self::TYPE_BOOLEAN,
        "syncStudentCompanyName" => self::TYPE_STRING,
        "syncStudentOU" => self::TYPE_STRING,
        "syncStudentDefaultMemberOf" => self::TYPE_STRING,
        "syncUpdateMail" => self::TYPE_STRING,
        "intuneOrderIdPrefix" => self::TYPE_STRING,
        "jamfIpadPrefix" => self::TYPE_STRING,
        "adJobTitlePrefix" => self::TYPE_STRING,
        "adOuPart" => self::TYPE_STRING,
        "adSecGroupPart" => self::TYPE_STRING,
        "dynamicTeam" => self::TYPE_BOOLEAN,
        "eetjemeeKeyStudents" => self::TYPE_STRING,
        "eetjemeeKeyEmployee" => self::TYPE_STRING,
        "smartschoolSourceId" => self::TYPE_STRING,
        "smsSyncClassTeachers" => self::TYPE_BOOLEAN,
        "smsGroupStudents" => self::TYPE_STRING,
        "smsGroupEmployee" => self::TYPE_STRING,
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
        $this->formatted->icon->sync = HTML::Icon($this->syncEmployee ? "check" : "x", "Personeel", $this->syncEmployee ? "green" : "red") . HTML::Icon($this->syncStudent ? "check" : "x", "Leerling", $this->syncStudent ? "green" : "red");

        $this->formatted->address = CString::formatAddress($this->street, $this->number, $this->bus, $this->zipcode, $this->city, $this->linked->country->name);
        $this->formatted->addressDoubleLine = CString::formatAddress($this->street, $this->number, $this->bus, $this->zipcode, $this->city, $this->linked->country->name, true);
        $this->formatted->addressWithSchool = "{$this->name} - {$this->formatted->address}";
    }
}
