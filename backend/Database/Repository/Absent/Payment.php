<?php

namespace Database\Repository\Absent;

use Database\Interface\Repository;

class Payment extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_absent_payment", \Database\Object\Absent\Payment::class, orderField: "name", deletedField: false, guidField: false);
    }
}
