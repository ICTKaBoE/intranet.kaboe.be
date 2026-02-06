<?php

namespace Database\Object\Signage;

use Security\CustomObject;

class Playlist extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "assignedTo" => self::TYPE_STRING,
        "assignedToId" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
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
