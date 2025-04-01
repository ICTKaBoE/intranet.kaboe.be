<?php

namespace Database\Object\Signage;

use Database\Interface\CustomObject;

class Playlist extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "name" => "string",
        "assignedTo" => "string",
        "assignedToId" => "int",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "screen" => ["assignedToId" => \Database\Repository\Signage\Screen::class],
        "group" => ["assignedToId" => \Database\Repository\Signage\Group::class]
    ];

    public function init()
    {
        $this->formatted->assignedTo = ($this->assignedTo == "S" ? "Scherm" : "Groep") . " - " . ($this->assignedTo == "S" ? $this->linked->screen->name : $this->linked->group->name);
    }
}
