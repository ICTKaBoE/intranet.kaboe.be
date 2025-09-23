<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;
use Helpers\General;
use Helpers\HTML;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class Schoolyear extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "name" => "string",
        "visibleFrom" => "date",
        "visibleUntil" => "date",
        "deleted" => "boolean"
    ];

    public function init()
    {
        $this->current = General::getSchoolyear(lengthLast: 4) == $this->name;
        $this->visible = $this->current || Clock::now()->isAfterOrEqualTo(Clock::at($this->visibleFrom)) && Clock::now()->isBeforeOrEqualTo(Clock::at($this->visibleUntil));
        $this->start = General::getSchoolyearStartBySchoolyear($this->name);
        $this->end = General::getSchoolyearStartBySchoolyear($this->name);
        $this->formatted->nameWithCurrent = $this->name . ($this->current ? " (huidig)" : "");
        $this->formatted->short = Arrays::first(explode("-", $this->name)) . "-" . substr(Arrays::last(explode("-", $this->name)), 2);

        $this->formatted->icon->current = HTML::Icon($this->current ? "check" : "x", color: $this->current ? "green" : "red");
        $this->formatted->icon->visible = HTML::Icon($this->visible ? "eye" : "eye-closed", color: $this->visible ? "green" : "red");

        $this->startAt = General::getSchoolyearStartBySchoolyear($this->name);
    }
}
