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
}
