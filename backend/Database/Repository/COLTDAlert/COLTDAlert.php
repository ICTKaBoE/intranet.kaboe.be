<?php

namespace Database\Repository\COLTDAlert;

use Database\Interface\Repository;

class COLTDAlert extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_coltdalert", \Database\Object\COLTDAlert\COLTDAlert::class, orderField: "datetime", orderDirection: self::ORDER_DIRECTION_DESC);
    }
}
