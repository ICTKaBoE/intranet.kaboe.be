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
        $this->formatted->emailLink = HTML::Link(HTML::LINK_TYPE_EMAIL, $this->email, $this->email);
        $this->formatted->typeWithEmailLink = "{$this->type}: {$this->formatted->emailLink}";
    }
}
