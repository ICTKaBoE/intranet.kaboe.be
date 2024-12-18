<?php

namespace Database\Object;

use stdClass;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;

class GeneralMessage extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "navigationId" => "int",
        "from" => "datetime",
        "until" => "datetime",
        "content" => "string",
        "type" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "navigation" => ["navigationId" => \Database\Repository\Navigation::class]
    ];

    public function init()
    {
        $this->mapped->type = Arrays::first((new Navigation)->getByParentIdAndLink(0, "configuration"))->settings['messages']['type'][$this->type]['name'];

        $this->show = $this->deleted ? false : (Clock::now()->isAfterOrEqualTo(Clock::at($this->from)) && (is_null($this->until) || Clock::now()->isBeforeOrEqualTo(Clock::at($this->until))));
        $this->formatted->type = (Strings::equalsIgnoreCase($this->type, "I") ? "success" : (Strings::equalsIgnoreCase($this->type, "W") ? "warning" : "danger"));
        $this->formatted->typeIcon = (Strings::equalsIgnoreCase($this->type, "I") ? "check" : (Strings::equalsIgnoreCase($this->type, "W") ? "alert-triangle" : "alert-circle"));

        $this->formatted->from = new stdClass;
        $this->formatted->from->display = Clock::at($this->from)->format("d/m/Y H:i:s");
        $this->formatted->from->sort = Clock::at($this->from)->format("u");

        $this->formatted->until = new stdClass;
        $this->formatted->until->display = $this->until ? Clock::at($this->until)->format("d/m/Y H:i:s") : "";
        $this->formatted->until->sort = $this->until ? Clock::at($this->until)->format("u") : "";
    }
}
