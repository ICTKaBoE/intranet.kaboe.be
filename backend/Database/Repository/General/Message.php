<?php

namespace Database\Repository\General;

use Database\Interface\Repository;

class Message extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_general_message", \Database\Object\General\Message::class, orderField: 'from', orderDirection: 'DESC');
    }

    public function getByNavigationId($navigationId)
    {
        $statement = $this->prepareSelect(filters: ['navigationId' => [$navigationId, 0]]);
        return $this->executeSelect($statement);
    }
}
