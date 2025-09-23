<?php

namespace Database\Repository\Helpdesk;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Priority extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_helpdesk_priority", \Database\Object\Helpdesk\Priority::class, orderField: "id", orderDirection: "DESC", deletedField: false, guidField: false);
    }
}
