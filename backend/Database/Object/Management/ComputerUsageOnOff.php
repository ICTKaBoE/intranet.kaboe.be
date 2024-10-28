<?php

namespace Database\Object\Management;

use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;
use Ouzo\Utilities\Strings;
use stdClass;

class ComputerUsageOnOff extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "computerId" => "int",
        "startup" => "datetime",
        "shutdown" => "datetime",
        "deleted" => "boolean"
    ];

    public function init()
    {
        if (Strings::startsWith($this->shutdown, '-')) $this->shutdown = false;
        $this->formatted->duration = ($this->shutdown ? Clock::at($this->shutdown)->toDateTime()->diff(Clock::at($this->startup)->toDateTime())->format("%a %H:%I:%S") : "N/A");

        $this->formatted->startup = new stdClass;
        $this->formatted->startup->display = Clock::at($this->startup)->format("d/m/Y H:i:s");
        $this->formatted->startup->sort = Clock::at($this->startup)->format("U");

        $this->formatted->shutdown = new stdClass;
        $this->formatted->shutdown->display = $this->shutdown ? Clock::at($this->shutdown)->format("d/m/Y H:i:s") : "";
        $this->formatted->shutdown->sort = Clock::at($this->shutdown)->format("U");
    }
}
