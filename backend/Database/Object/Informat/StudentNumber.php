<?php

namespace Database\Object\Informat;

use Helpers\HTML;
use Security\CustomObject;

class StudentNumber extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "informatId" => self::TYPE_INTEGER,
        "number" => self::TYPE_STRING,
        "type" => self::TYPE_STRING,
        "category" => self::TYPE_STRING
    ];

    public function init()
    {
        $this->formatted->details = "{$this->type} - {$this->category}:\t{$this->number}";

        $this->formatted->link = HTML::Link(HTML::LINK_TYPE_PHONE, $this->number);
        $this->formatted->typeWithLink = "{$this->type} - {$this->category}:\t{$this->formatted->link}";
    }
}
