<?php

namespace Database\Repository\Helpdesk;

use Database\Interface\Repository;

class Ticket extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_helpdesk_ticket", \Database\Object\Helpdesk\Ticket::class, orderField: 'lastActionDateTime');
    }

    public function getByCreatorUserId($creatorUserId)
    {
        $statement = $this->prepareSelect();
        $statement->where('creatorUserId', $creatorUserId);

        return $this->executeSelect($statement);
    }

    public function getByAssignedToUserId($assignedToUserId)
    {
        $statement = $this->prepareSelect();
        $statement->where('assignedToUserId', $assignedToUserId);

        return $this->executeSelect($statement);
    }
}
