<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;

class StudentBank extends CustomObject
{
    const MAPPING_TYPE = [
        1 => "Persoonlijk",
        2 => "Ouders",
        3 => "Vader",
        4 => "Moeder",
        6 => "Voogd",
        8 => "Andere"
    ];

    protected $objectAttributes = [
        "id" => "int",
        "informatStudentId" => "int",
        "type" => "string",
        "iban" => "string",
        "bic" => "string"
    ];

    public function init()
    {
        $this->formatted->details = self::MAPPING_TYPE[$this->type] . ": {$this->iban} - {$this->bic}";
    }
}
