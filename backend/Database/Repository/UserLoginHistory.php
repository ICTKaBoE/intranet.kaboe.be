<?php

namespace Database\Repository;

use Database\Interface\Repository;

class UserLoginHistory extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_user_login_history", \Database\Object\UserLoginHistory::class, orderField: "timestamp", orderDirection: 'DESC', deletedField: false);
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);

        return $this->executeSelect($statement);
    }
}
