<?php

namespace Database\Object;

use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;

class HelpdeskMessage extends CustomObject
{
    protected $objectAttributes = [
        "id",
        "helpdeskId",
        "upn",
        "name",
        "message",
        "internal",
        "timestamp",
        "deleted"
    ];

    public function init()
    {
        $age = Clock::at($this->timestamp)->toDateTime()->diff(Clock::now()->toDateTime());
        if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0 && $age->i == 0) $this->age = $age->s . " seconden geleden";
        else if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0) $this->age = $age->i . " minuten geleden";
        else if ($age->y == 0 && $age->m == 0 && $age->d == 0) $this->age = $age->h . " uren geleden";
        else if ($age->y == 0 && $age->m == 0) $this->age = $age->d . " dagen geleden";
        else if ($age->y == 0) $this->age = $age->m . " maanden geleden";
        else $this->age = $age->y . " jaren geleden";
    }
}
