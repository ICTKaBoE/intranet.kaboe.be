<?php

namespace Database\Repository\Violence;

use Database\Interface\Repository;

class Intention extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_violence_intention", \Database\Object\Violence\Intention::class, orderField: 'name', deletedField: false, guidField: false);
    }
}
