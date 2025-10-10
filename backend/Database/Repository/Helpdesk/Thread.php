<?php

namespace Database\Repository\Helpdesk;

use Database\Interface\Repository;

class Thread extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_helpdesk_thread", \Database\Object\Helpdesk\Thread::class, orderField: 'creationDateTime', orderDirection: self::ORDER_DIRECTION_DESC, guidField: false);
    }

    public function getByTicketId($ticketId)
    {
        $statement = $this->prepareSelect(filters: [
            'ticketId' => $ticketId
        ]);

        return $this->executeSelect($statement);
    }
}
