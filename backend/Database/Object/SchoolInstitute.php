<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class SchoolInstitute extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "schoolId" => "int",
        "number" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => [
            "schoolId" => \Database\Repository\School::class
        ]
    ];

    public function init()
    {
        $this->numberNewFormat = (strlen($this->number) == 5 ? "0" : "") . $this->number;
    }
}
