<?php

namespace Database\Repository\User;

use Database\Interface\Repository;

class LoginHistory extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_user_login_history", \Database\Object\User\LoginHistory::class, orderField: "timestamp", orderDirection: 'DESC', deletedField: false, guidField: false);
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);

        return $this->executeSelect($statement);
    }
}
