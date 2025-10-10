<?php

namespace Database\Object\School;

use Database\Interface\CustomObject;
use Helpers\HTML;

class School extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "virtual" => self::TYPE_BOOLEAN,
        "parentSchoolId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "color" => self::TYPE_STRING,
        "intuneOrderIdPrefix" => self::TYPE_STRING,
        "jamfIpadPrefix" => self::TYPE_STRING,
        "adJobTitlePrefix" => self::TYPE_STRING,
        "adOuPart" => self::TYPE_STRING,
        "adSecGroupPart" => self::TYPE_STRING,
        "syncUpdateMail" => self::TYPE_LIST,
        "dynamicTeam" => self::TYPE_BOOLEAN,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "parentSchool" => ['parentSchoolId' => \Database\Repository\School\School::class]
    ];

    public function init()
    {
        $this->formatted->nameWithParent = ($this->linked->parentSchool ? $this->linked->parentSchool->name . " - " : "") . $this->name;
        $this->formatted->badge->name = HTML::Badge($this->name, style: [
            "margin-top" => "2px",
            "background-color" => $this->color
        ]);

        $this->formatted->icon->virtual = HTML::Icon($this->virtual ? "cloud" : "building");
    }
}
