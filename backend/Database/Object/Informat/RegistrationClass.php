<?php

namespace Database\Object\Informat;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Clock;

class RegistrationClass extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "informatGuid" => "string",
        "informatRegistrationId" => "int",
        "informatClassGroupId" => "int",
        "rank" => "int",
        "start" => "date",
        "end" => "date",
        "current" => "bool"
    ];

    protected $linkedAttributes = [
        "informatRegistration" => ["informatRegistrationId" => \Database\Repository\Informat\Registration::class]
    ];

    public function init()
    {
        $this->formatted->dates = Clock::at($this->start)->format("d/m/Y") . (is_null($this->end) ? "" : " - " . Clock::at($this->end)->format("d/m/Y"));
    }
}
