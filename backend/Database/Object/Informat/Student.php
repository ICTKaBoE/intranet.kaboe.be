<?php

namespace Database\Object\Informat;

use Security\Input;
use Helpers\CString;
use Security\CustomObject;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

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
        "insz" => self::TYPE_STRING,
        "instituteId" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "institute" => ["instituteId" => \Database\Repository\School\Institute::class]
    ];

    // protected $decodeAttributes = [
    //     "name",
    //     "firstName"
    // ];

    public function init()
    {
        $this->formatted->informatGuidOrId = $this->informatGuid ?: $this->id;

        $this->formatted->insz = Input::formatInsz($this->insz);
        $this->formatted->fullName = Input::createDisplayName("{{FN}} {{LN}}", $this->firstName, $this->name);
        $this->formatted->fullNameReversed = Input::createDisplayName("{{LN}} {{FN}}", $this->firstName, $this->name);

        $this->formatted->initials = Input::clean(CString::firstLetterOfEachWord($this->formatted->fullName));
        $this->formatted->initialsIfNoPhoto = file_exists(LOCATION_IMAGE . "/informat/student/{$this->informatGuid}.jpg") ? "" : $this->formatted->initials;

        $this->formatted->birthDate = Clock::at($this->birthDate)->format("d/m/Y");
        $this->formatted->birthDateDay = Clock::at($this->birthDate)->format("d");
        $this->formatted->birthDateMonth = Clock::at($this->birthDate)->format("m");
        $this->formatted->birthDateYear = Clock::at($this->birthDate)->format("Y");
        $this->formatted->sex = (Strings::equal($this->sex, "M") ? "zoon" : (Strings::equal($this->sex, "F") ? "dochter" : "het"));
        $this->formatted->sexInWords = (Strings::equal($this->sex, "M") ? "Man" : "Vrouw");
    }
}
