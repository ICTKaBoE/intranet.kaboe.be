<?php

namespace Informat\Object;

use Security\CustomObject;

class StudentPhoto extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "persoonId" => "string",
        "foto" => "base64"
    ];
}
