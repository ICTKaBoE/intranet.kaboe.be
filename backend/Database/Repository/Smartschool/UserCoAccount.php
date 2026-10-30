<?php

namespace Database\Repository\Smartschool;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class UserCoAccount extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_smartschool_user_coaccount", \Database\Object\Smartschool\UserCoAccount::class, orderField: false, guidField: false, deletedField: false);
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect(filters: ['userId' => $userId]);
        return $this->executeSelect($statement);
    }

    public function getByUserIdAndIndex($userId, $index)
    {
        $statement = $this->prepareSelect(filters: ['userId' => $userId, 'index' => $index]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
