<?php

namespace Database\Object\School;

use Database\Interface\CustomObject;

class Institute extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "number" => self::TYPE_STRING,
        "sourceId" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        // "school" => [
        //     "schoolId" => \Database\Repository\School\School::class
        // ]
    ];

    public function init()
    {
        $this->numberNewFormat = (strlen($this->number) == 5 ? "0" : "") . $this->number;
    }
}
