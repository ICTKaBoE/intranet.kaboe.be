<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;

class SupervisionEvent extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "userId" => "int",
        "schoolId" => "int",
        "start" => "datetime",
        "end" => "datetime",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "user" => ['userId' => \Database\Repository\User::class],
        "school" => ["schoolId" => \Database\Repository\School::class]
    ];

    public function init()
    {
        $start = Clock::at($this->start)->toDateTime();
        $end = Clock::at($this->end)->toDateTime();
        $diff = $start->diff($end);

        $this->diffInMinutes = (($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i);
    }
}
