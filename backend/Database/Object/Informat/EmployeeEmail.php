<?php

namespace Database\Object\Informat;

use Security\CustomObject;
use Helpers\HTML;

class EmployeeEmail extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatEmployeeId" => self::TYPE_INTEGER,
        "informatGuid" => self::TYPE_STRING,
        "email" => self::TYPE_STRING,
        "type" => self::TYPE_STRING
    ];

    public function init()
    {
        $this->formatted->link = HTML::Link(HTML::LINK_TYPE_EMAIL, $this->email);
    }
}
