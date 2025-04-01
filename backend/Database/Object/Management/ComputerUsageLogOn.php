<?php

namespace Database\Object\Management;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Database\Interface\CustomObject;

class ComputerUsageLogOn extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "computerId" => "int",
        "username" => "string",
        "logon" => "datetime",
        "logoff" => "datetime",
        "deleted" => "boolean"
    ];

    public function init()
    {
        if (Strings::startsWith($this->logoff, '-')) $this->logoff = false;

        $this->formatted->duration = ($this->logoff ? Clock::at($this->logoff)->toDateTime()->diff(Clock::at($this->logon)->toDateTime())->format("%a %H:%I:%S") : "N/A");
        $this->formatted->logon = Clock::at($this->logon)->format("d/m/Y H:i:s");
        $this->formatted->logoff = Clock::at($this->logoff)->format("d/m/Y H:i:s");
    }
}
