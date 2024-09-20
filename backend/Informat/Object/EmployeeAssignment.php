<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;
use Ouzo\Utilities\Clock;

class EmployeeAssignment extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "personId" => "string",
        "begindatum" => "date",
        "einddatum" => "date"
    ];

    public function init()
    {
        if (Clock::at($this->einddatum)->format("m-d") == "06-30") $this->einddatum = Clock::at($this->einddatum)->format("Y-08-31");
    }
}
