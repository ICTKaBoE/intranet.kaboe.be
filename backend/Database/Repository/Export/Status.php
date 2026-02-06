<?php

namespace Database\Repository\Export;

use Database\Interface\Repository;

class Status extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_export_status", \Database\Object\Export\Status::class, orderField: "name", deletedField: false, guidField: false);
    }
}
