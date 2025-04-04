<?php

namespace Database\Object\School;

use Database\Interface\CustomObject;
use Helpers\HTML;

class School extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "name" => "string",
        "color" => "string",
        "intuneOrderIdPrefix" => "string",
        "jamfIpadPrefix" => "string",
        "adJobTitlePrefix" => "string",
        "adOuPart" => "string",
        "adSecGroupPart" => "string",
        "syncUpdateMail" => "list",
        "dynamicTeam" => "boolean",
        "deleted" => "boolean"
    ];

    public function init()
    {
        $this->formatted->badge->name = HTML::Badge($this->name, style: [
            "margin-top" => "2px",
            "background-color" => $this->color
        ]);
    }
}
