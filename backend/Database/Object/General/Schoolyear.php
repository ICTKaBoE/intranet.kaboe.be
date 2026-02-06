<?php

namespace Database\Object\General;

use Security\CustomObject;

class Schoolyear extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "name" => self::TYPE_STRING,
        "start" => self::TYPE_DATE,
        "end" => self::TYPE_DATE,
        "current" => self::TYPE_BOOLEAN
    ];

    public function init()
    {
        $this->formatted->nameWithCurrent = $this->name . ($this->current ? " (huidig)" : "");
    }
}
