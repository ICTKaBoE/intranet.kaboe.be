<?php

namespace Database\Object\School;

use Database\Interface\CustomObject;

class Institute extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "schoolId" => "int",
        "number" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => [
            "schoolId" => \Database\Repository\School\School::class
        ]
    ];

    public function init()
    {
        $this->numberNewFormat = (strlen($this->number) == 5 ? "0" : "") . $this->number;
    }
}
