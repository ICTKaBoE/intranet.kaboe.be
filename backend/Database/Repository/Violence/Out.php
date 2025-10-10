<?php

namespace Database\Repository\Violence;

use Database\Interface\Repository;

class Out extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_violence_out", \Database\Object\Violence\Out::class, orderField: 'name', deletedField: false, guidField: false);
    }
}
