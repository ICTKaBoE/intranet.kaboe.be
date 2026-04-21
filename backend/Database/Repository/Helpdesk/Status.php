<?php

namespace Database\Repository\Helpdesk;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Status extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_helpdesk_status", \Database\Object\Helpdesk\Status::class, deletedField: false, guidField: false);
    }

    public function getNew()
    {
        $statement = $this->prepareSelect(filters: ['new' => 1]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getReopen()
    {
        $statement = $this->prepareSelect(filters: ['reopen' => 1]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getClose()
    {
        $statement = $this->prepareSelect(filters: ['close' => 1]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
