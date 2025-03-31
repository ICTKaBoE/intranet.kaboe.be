<?php

namespace Database\Repository\Signage;

use Database\Interface\Repository;

class Group extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_signage_group", \Database\Object\Signage\Group::class, orderField: 'name');
    }
}
