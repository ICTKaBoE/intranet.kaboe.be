<?php

namespace Database\Object\Helpdesk;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Clock;

class Thread extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "ticketId" => "int",
        "creationDateTime" => "datetime",
        "creatorId" => "int",
        "content" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        // "ticket" => ["ticketId" => \Database\Repository\Helpdesk\Ticket::class],
        "creator" => ["creatorId" => \Database\Repository\User\User::class],
    ];

    public function init()
    {
        $this->formatted->creationDateTime = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
    }
}
