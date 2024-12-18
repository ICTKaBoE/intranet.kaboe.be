<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class StudentPhoto extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "persoonId" => "string",
        "foto" => "base64"
    ];
}
