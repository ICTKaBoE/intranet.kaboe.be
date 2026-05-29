<?php

namespace Database\Repository\HR;

use Database\Interface\Repository;

class Status extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_hr_status", \Database\Object\HR\Status::class, orderField: "name", guidField: false);
    }
}
