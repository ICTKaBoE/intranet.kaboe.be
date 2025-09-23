<?php

namespace Database\Object\Order;

use Helpers\HTML;
use Database\Interface\CustomObject;

class Status extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
        "color" => "string"
    ];

    public function init()
    {
        $this->formatted->badge->name = HTML::Badge($this->name, style: [
            "margin-top" => "2px",
            "background-color" => $this->color
        ]);
    }
}
