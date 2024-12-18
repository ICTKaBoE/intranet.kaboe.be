<?php

namespace Database\Object\Informat;

use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;

class Registration extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatId" => "int",
        "informatGuid" => "string",
        "informatStudentId" => "int",
        "schoolInstituteId" => "int",
        "basenumber" => "string",
        "start" => "date",
        "end" => "date",
        "status" => "int"
    ];

    public function init()
    {
        $this->formatted->dates = Clock::at($this->start)->format("d/m/Y") . (is_null($this->end) ? "" : " - " . Clock::at($this->end)->format("d/m/Y"));
    }
}
