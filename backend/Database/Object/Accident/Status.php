<?php

namespace Database\Object\Accident;

use Helpers\HTML;
use Security\CustomObject;

class Status extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "color" => self::TYPE_STRING,
        "order" => self::TYPE_INTEGER,
        "default" => self::TYPE_BOOLEAN,
        "whenInsuranceIsMailed" => self::TYPE_BOOLEAN,
        "closedNoFollow" => self::TYPE_BOOLEAN,
        "closed" => self::TYPE_BOOLEAN,
    ];

    public function init()
    {
        $this->formatted->badge->name = HTML::Badge($this->name, style: [
            "margin-top" => "2px",
            "background-color" => $this->color
        ]);
    }
}
