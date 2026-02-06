<?php

namespace Database\Object\Helpdesk;

use Security\CustomObject;
use Ouzo\Utilities\Clock;

class Thread extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "ticketId" => self::TYPE_INTEGER,
        "creationDateTime" => self::TYPE_DATETIME,
        "creatorId" => self::TYPE_INTEGER,
        "content" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
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
