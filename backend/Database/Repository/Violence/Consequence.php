<?php

namespace Database\Repository\Violence;

use Database\Interface\Repository;

class Consequence extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_violence_consequence", \Database\Object\Violence\Consequence::class, orderField: 'name', deletedField: false, guidField: false);
    }
}
