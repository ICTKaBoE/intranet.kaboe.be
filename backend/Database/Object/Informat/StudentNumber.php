<?php

namespace Database\Object\Informat;

use Helpers\HTML;
use Database\Interface\CustomObject;

class StudentNumber extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatStudentId" => "int",
        "informatId" => "int",
        "number" => "string",
        "type" => "string",
        "category" => "string"
    ];

    public function init()
    {
        $this->formatted->numberLink = HTML::Link(HTML::LINK_TYPE_PHONE, $this->number, $this->number);
        $this->formatted->typeWithNumberLink = "{$this->type} ({$this->category}): {$this->formatted->numberLink}";
    }
}
