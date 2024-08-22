<?php

namespace Database\Repository;

use Database\Interface\Repository;

class SecurityGroupUser extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_security_group_user", \Database\Object\SecurityGroupUser::class, orderField: false, deletedField: false);
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);

        return $this->executeSelect($statement);
    }
}
