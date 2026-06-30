<?php

namespace Database\Object\Informat;

use Security\CustomObject;
use Ouzo\Utilities\Clock;

class RegistrationClass extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "informatGuid" => self::TYPE_STRING,
        "informatRegistrationId" => self::TYPE_INTEGER,
        "informatClassGroupId" => self::TYPE_INTEGER,
        "rank" => self::TYPE_INTEGER,
        "start" => self::TYPE_DATE,
        "virtualStart" => self::TYPE_DATE,
        "end" => self::TYPE_DATE,
        "virtualEnd" => self::TYPE_DATE,
        "current" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "informatRegistration" => ["informatRegistrationId" => \Database\Repository\Informat\Registration::class]
    ];

    public function init()
    {
        $this->formatted->dates = Clock::at($this->start)->format("d/m/Y") . (is_null($this->end) ? "" : " - " . Clock::at($this->end)->format("d/m/Y"));
    }
}
