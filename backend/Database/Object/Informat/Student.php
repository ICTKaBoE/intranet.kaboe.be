<?php

namespace Database\Object\Informat;

use Security\Input;
use Helpers\CString;
use Database\Interface\CustomObject;
use Helpers\General;
use Ouzo\Utilities\Clock;

class Student extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatId" => self::TYPE_INTEGER,
        "informatGuid" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "firstName" => self::TYPE_STRING,
        "sex" => self::TYPE_STRING,
        "birthDate" => self::TYPE_DATE,
        "birthPlace" => self::TYPE_STRING,
        "isdn" => self::TYPE_STRING,
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

        $this->formatted->initials = Input::clean(CString::firstLetterOfEachWord($this->formatted->fullName));
        $this->formatted->initialsIfNoPhoto = file_exists(LOCATION_IMAGE . "/informat/student/{$this->informatGuid}.jpg") ? "" : $this->formatted->initials;

        $this->formatted->birthDate = Clock::at($this->birthDate)->format("d/m/Y");
    }
}
