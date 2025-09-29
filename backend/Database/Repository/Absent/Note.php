<?php

namespace Database\Repository\Absent;

use Database\Interface\Repository;

class Note extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_absent_note", \Database\Object\Absent\Note::class, orderField: "name", deletedField: false, guidField: false);
    }
}
