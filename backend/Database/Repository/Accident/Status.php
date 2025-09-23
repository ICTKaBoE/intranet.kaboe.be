<?php

namespace Database\Repository\Accident;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Status extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_accident_status", \Database\Object\Accident\Status::class, deletedField: false, guidField: false);
    }
}
