<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;
use Helpers\HTML;

class EmployeeEmail extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatEmployeeId" => "int",
        "informatGuid" => "string",
        "email" => "string",
        "type" => "string"
    ];

    public function init()
    {
        $this->formatted->link = HTML::Link(HTML::LINK_TYPE_EMAIL, $this->email);
    }
}
