<?php

namespace Database\Object\Informat;

use Security\Input;
use Helpers\CString;
use Database\Interface\CustomObject;
use Ouzo\Utilities\Clock;

class Student extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatId" => "int",
        "informatGuid" => "string",
        "name" => "string",
        "firstName" => "string",
        "sex" => "string",
        "birthDate" => "date",
        "birthPlace" => "string",
        "isdn" => "string",
    ];

    // protected $decodeAttributes = [
    //     "name",
    //     "firstName"
    // ];

    public function init()
    {
        $this->formatted->informatGuidOrId = $this->informatGuid ?: $this->id;

        $this->formatted->fullName = Input::createDisplayName("{{FN}} {{LN}}", $this->firstName, $this->name);
        $this->formatted->fullNameReversed = Input::createDisplayName("{{LN}} {{FN}}", $this->firstName, $this->name);

        $this->formatted->initials = CString::firstLetterOfEachWord($this->formatted->fullName);
        $this->formatted->initialsIfNoPhoto = file_exists(LOCATION_IMAGE . "/informat/student/{$this->informatGuid}.jpg") ? "" : $this->formatted->initials;

        $this->formatted->birthDate = Clock::at($this->birthDate)->format("d/m/Y");
    }
}
