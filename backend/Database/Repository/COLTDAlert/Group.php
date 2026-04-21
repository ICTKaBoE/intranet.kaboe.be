<?php

namespace Database\Repository\COLTDAlert;

use Database\Interface\Repository;

class Group extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_coltdalert_group", \Database\Object\COLTDAlert\Group::class, orderField: 'name');
    }
}
