<?php

namespace Database\Repository\HR;

use Database\Interface\Repository;

class Role extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_hr_role", \Database\Object\HR\Role::class, orderField: "name");
    }
}
