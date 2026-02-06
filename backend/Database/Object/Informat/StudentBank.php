<?php

namespace Database\Object\Informat;

use Security\CustomObject;
use Helpers\CString;

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
        "id" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "type" => self::TYPE_STRING,
        "iban" => self::TYPE_STRING,
        "bic" => self::TYPE_STRING
    ];

    public function init()
    {
        $this->formatted->iban = CString::formatBankAccount($this->iban);
        $this->formatted->bic = CString::formatBankId($this->bic);
        $this->formatted->details = self::MAPPING_TYPE[$this->type] . ":\t{$this->formatted->iban} - {$this->formatted->bic}";
    }
}
