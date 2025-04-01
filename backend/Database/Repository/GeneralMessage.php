<?php

namespace Database\Repository;

use Database\Interface\Repository;

class GeneralMessage extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_general_message", \Database\Object\GeneralMessage::class, orderField: 'from', orderDirection: 'DESC');
    }

    public function getByNavigationId($navigationId)
    {
        $statement = $this->prepareSelect();
        $statement->where('navigationId', $navigationId);
        $statement->orWhere('navigationId', 0);

        return $this->executeSelect($statement);
    }
}
