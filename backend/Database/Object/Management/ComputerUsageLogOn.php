<?php

namespace Database\Object\Management;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Database\Interface\CustomObject;

class ComputerUsageLogOn extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "computerId" => self::TYPE_INTEGER,
        "username" => self::TYPE_STRING,
        "logon" => self::TYPE_DATETIME,
        "logoff" => self::TYPE_DATETIME,
        "deleted" => self::TYPE_BOOLEAN
    ];

    public function init()
    {
        if (Strings::startsWith($this->logoff, '-')) $this->logoff = false;

        $this->formatted->duration = ($this->logoff ? Clock::at($this->logoff)->toDateTime()->diff(Clock::at($this->logon)->toDateTime())->format("%a %H:%I:%S") : "N/A");
        $this->formatted->logon = Clock::at($this->logon)->format("d/m/Y H:i:s");
        $this->formatted->logoff = Clock::at($this->logoff)->format("d/m/Y H:i:s");
    }
}
