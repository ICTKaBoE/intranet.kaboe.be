<?php

namespace Database\Object\Accident;

use Database\Interface\CustomObject;
use Database\Repository\Navigation\Navigation;
use Helpers\HTML;

class Document extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "alias" => "string",
        "name" => "string",
        "ext" => "string",
        "deleted" => "boolean"
    ];
}
