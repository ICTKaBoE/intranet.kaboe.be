<?php

namespace Database\Object\Informat;

use Security\Input;
use Helpers\CString;
use Database\Interface\CustomObject;

class Employee extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatId" => self::TYPE_INTEGER,
        "informatGuid" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "firstName" => self::TYPE_STRING,
        "extraFirstName" => self::TYPE_STRING,
        "basenumber" => self::TYPE_STRING,
        "sex" => self::TYPE_STRING,
        "birthDate" => self::TYPE_DATE,
        "birthPlace" => self::TYPE_STRING,
        "birthCountryId" => self::TYPE_STRING,
        "nationalityId" => self::TYPE_STRING,
        "insz" => self::TYPE_STRING,
        "bis" => self::TYPE_STRING,
        "iban" => self::TYPE_STRING,
        "bic" => self::TYPE_STRING,
        "active" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "birthCountry" => [
            "birthCountryId" => \Database\Repository\General\Country::class
        ],
        "nationality" => [
            "nationalityId" => \Database\Repository\General\Country::class
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
