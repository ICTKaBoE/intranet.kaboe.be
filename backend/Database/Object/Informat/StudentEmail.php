<?php

namespace Database\Object\Informat;

use Security\CustomObject;
use Helpers\HTML;

class StudentEmail extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "informatId" => self::TYPE_INTEGER,
        "email" => self::TYPE_STRING,
        "type" => self::TYPE_STRING
    ];

    public function init()
    {
        $this->formatted->typeWithEmail = "{$this->type}:\t{$this->email}";

        $this->formatted->link = HTML::Link(HTML::LINK_TYPE_EMAIL, $this->email);
        $this->formatted->typeWithLink = "{$this->type}:\t{$this->formatted->link}";
    }
}
