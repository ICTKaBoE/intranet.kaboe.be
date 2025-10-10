<?php

namespace Database\Repository\Violence;

use Database\Interface\Repository;

class DamageKind extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_violence_damage_kind", \Database\Object\Violence\DamageKind::class, orderField: 'name', deletedField: false, guidField: false);
    }
}
