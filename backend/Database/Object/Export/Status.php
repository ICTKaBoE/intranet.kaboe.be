<?php

namespace Database\Object\Export;

use Helpers\HTML;
use Security\CustomObject;

class Status extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "color" => self::TYPE_STRING
    ];

    public function init()
    {
        $this->formatted->badge->name = HTML::Badge($this->name, backgroundColor: $this->color);
    }
}
