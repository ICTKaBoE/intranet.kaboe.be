<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Security\CustomObject;

class SupervisionEvent extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "userId" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "start" => self::TYPE_DATETIME,
        "end" => self::TYPE_DATETIME,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        // "user" => ['userId' => \Database\Repository\User\User::class],
        "school" => ["schoolId" => \Database\Repository\School\School::class]
    ];

    public function init()
    {
        $start = Clock::at($this->start)->toDateTime();
        $end = Clock::at($this->end)->toDateTime();
        $diff = $start->diff($end);

        $this->diffInMinutes = (($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i);
    }
}
