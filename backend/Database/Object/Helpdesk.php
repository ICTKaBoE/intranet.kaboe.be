<?php

namespace Database\Object;

use Core\Config;
use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;

class Helpdesk extends CustomObject
{
    protected $objectAttributes = [
        "id",
        "priority",
        "status",
        "creatorUpn",
        "creatorName",
        "assignedToUpn",
        "assignedToName",
        "schoolId",
        "schoolName",
        "type",
        "deviceName",
        "subject",
        "lastActionTimestamp",
        "deleted"
    ];

    public function init()
    {
        $this->statusHtml = "<span class='badge bg-" . Config::get("tool/helpdesk/status/{$this->status}/color") . "'>" . Config::get("tool/helpdesk/status/{$this->status}/name") . "</span>";
        $this->priorityHtml = "<span class='badge bg-" . Config::get("tool/helpdesk/priority/{$this->priority}/color") . "'>" . Config::get("tool/helpdesk/priority/{$this->priority}/name") . "</span>";
        $this->typeName = Config::get("tool/helpdesk/type/{$this->type}");

        $age = Clock::at($this->lastActionTimestamp)->toDateTime()->diff(Clock::now()->toDateTime());
        if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0 && $age->i == 0) $this->age = $age->s . " seconden geleden";
        else if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0) $this->age = $age->i . " minuten geleden";
        else if ($age->y == 0 && $age->m == 0 && $age->d == 0) $this->age = $age->h . " uren geleden";
        else if ($age->y == 0 && $age->m == 0) $this->age = $age->d . " dagen geleden";
        else if ($age->y == 0) $this->age = $age->m . " maanden geleden";
        else $this->age = $age->y . " jaren geleden";
    }
}
