<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;
use Helpers\HTML;

class StudentEmail extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatStudentId" => "int",
        "informatId" => "int",
        "email" => "string",
        "type" => "string"
    ];

    public function init()
    {
        $this->formatted->typeWithEmail = "{$this->type}:\t{$this->email}";

        $this->formatted->link = HTML::Link(HTML::LINK_TYPE_EMAIL, $this->email);
        $this->formatted->typeWithLink = "{$this->type}:\t{$this->formatted->link}";
    }
}
