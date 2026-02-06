<?php

namespace Database\Object\School;

use Ouzo\Utilities\Clock;
use Security\CustomObject;

class Hour extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "start" => self::TYPE_TIME,
        "end" => self::TYPE_TIME,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class]
    ];

    public function init()
    {
        $this->formatted->start = Clock::at($this->start)->format("H:i");
        $this->formatted->end = Clock::at($this->end)->format("H:i");
        $this->formatted->startEnd = "{$this->formatted->start} - {$this->formatted->end}";
    }
}
