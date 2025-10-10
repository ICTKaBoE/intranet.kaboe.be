<?php

namespace Database\Repository\EHBO;

use Database\Interface\Repository;

class VictimType extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_ehbo_victimtype", \Database\Object\EHBO\VictimType::class, orderField: "name", deletedField: false, guidField: false);
    }
}
