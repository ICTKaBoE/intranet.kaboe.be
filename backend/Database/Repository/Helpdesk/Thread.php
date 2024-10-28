<?php

namespace Database\Repository\Helpdesk;

use Database\Interface\Repository;

class Thread extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_helpdesk_thread", \Database\Object\Helpdesk\Thread::class, orderField: 'creationDateTime', orderDirection: 'DESC', guidField: false);
    }

    public function getByTicketId($ticketId)
    {
        $statement = $this->prepareSelect();
        $statement->where('ticketId', $ticketId);

        return $this->executeSelect($statement);
    }
}
