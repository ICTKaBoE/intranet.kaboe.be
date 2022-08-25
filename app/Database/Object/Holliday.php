<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class Holliday extends CustomObject
{
    protected $objectAttributes = [
        'id',
        'name',
        'start',
        'end',
        'fullDay',
        'deleted'
    ];

    public function init()
    {
        $this->fullDay = ($this->fullDay == 1);

        if ($this->fullDay) {
            $this->start = Arrays::first(explode(" ", $this->start));
            $this->end = Arrays::first(explode(" ", $this->end));
            $this->end = Clock::at($this->end)->plusDays(1)->format("Y-m-d");
        }
    }
}
