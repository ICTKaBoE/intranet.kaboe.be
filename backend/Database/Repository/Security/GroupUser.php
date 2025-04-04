<?php

namespace Database\Repository\Security;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class GroupUser extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_security_group_user", \Database\Object\Security\GroupUser::class, orderField: false, deletedField: false, guidField: false);
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);

        return $this->executeSelect($statement);
    }

    public function getBySecurityGroupId($securityGroupId)
    {
        $statement = $this->prepareSelect();
        $statement->where('securityGroupId', $securityGroupId);

        return $this->executeSelect($statement);
    }

    public function getBySecurityGroupIdAndUserId($securityGroupId, $userId)
    {
        $statement = $this->prepareSelect();
        $statement->where('securityGroupId', $securityGroupId);
        $statement->where('userId', $userId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
