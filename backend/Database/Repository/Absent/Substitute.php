<?php

namespace Database\Repository\Absent;

use Database\Interface\Repository;

class Substitute extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_absent_substitute", \Database\Object\Absent\Substitute::class, orderField: "name", deletedField: false, guidField: false);
    }
}
