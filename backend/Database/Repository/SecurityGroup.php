<?php

namespace Database\Repository;

use Database\Interface\Repository;

class SecurityGroup extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_security_group", \Database\Object\SecurityGroup::class, orderField: 'name', deletedField: false, guidField: false);
    }
}
