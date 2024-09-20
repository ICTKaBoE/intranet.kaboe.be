<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Interface\CustomObject;

class Holliday extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "schoolId" => "int",
        "name" => "string",
        "start" => "datetime",
        "end" => "datetime",
        "fullDay" => "bool",
        "deleted" => "bool"
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School::class]
    ];

    public function init()
    {
        if ($this->fullDay) {
            $this->start = Arrays::first(explode(" ", $this->start));
            $this->end = Arrays::first(explode(" ", $this->end));

            if (!Strings::equal($this->start, $this->end)) $this->end = Clock::at($this->end)->plusDays(1)->format("Y-m-d");
        }
    }
}
