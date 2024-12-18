<?php

namespace Database\Object\Informat;

use Security\Input;
use Helpers\CString;
use Database\Interface\CustomObject;

class Employee extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatId" => "int",
        "informatGuid" => "string",
        "name" => "string",
        "firstName" => "string",
        "basenumber" => "string",
        "sex" => "string",
        "birthDate" => "date",
        "birthPlace" => "string",
        "birthCountryId" => "string",
        "nationalityId" => "string",
        "insz" => "string",
        "bis" => "string",
        "iban" => "string",
        "bic" => "string",
        "active" => "bool"
    ];

    protected $linkedAttributes = [
        "birthCountry" => [
            "birthCountryId" => \Database\Repository\Country::class
        ],
        "nationality" => [
            "nationalityId" => \Database\Repository\Country::class
        ]
    ];

    public function init()
    {
        $this->formatted->informatGuidOrId = $this->informatGuid ?: $this->id;

        $this->formatted->fullName = Input::createDisplayName("{{FN}} {{LN}}", $this->firstName, $this->name);
        $this->formatted->fullNameReversed = Input::createDisplayName("{{LN}} {{FN}}", $this->firstName, $this->name);

        $this->formatted->initials = CString::firstLetterOfEachWord($this->formatted->fullName);
        $this->formatted->initialsIfNoPhoto = file_exists(LOCATION_IMAGE . "/informat/employee/{$this->informatGuid}.jpg") ? "" : $this->formatted->initials;
    }
}
