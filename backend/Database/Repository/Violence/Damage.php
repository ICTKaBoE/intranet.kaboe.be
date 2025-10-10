<?php

namespace Database\Repository\Violence;

use Database\Interface\Repository;

class Damage extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_violence_damage", \Database\Object\Violence\Damage::class, orderField: 'name', deletedField: false, guidField: false);
    }
}
