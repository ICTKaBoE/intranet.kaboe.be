<?php

namespace Database\Repository\Security;

use Database\Interface\Repository;

class Group extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_security_group", \Database\Object\Security\Group::class, orderField: 'name', guidField: false);
    }
}
