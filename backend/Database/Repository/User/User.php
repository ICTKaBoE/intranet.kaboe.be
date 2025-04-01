<?php

namespace Database\Repository\User;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class User extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_user", \Database\Object\User\User::class, orderField: "name");
    }

    public function getByUsername($username)
    {
        $statement = $this->prepareSelect();
        $statement->where('username', $username);

        return $this->executeSelect($statement);
    }

    public function getByEntraId($entraId)
    {
        $statement = $this->prepareSelect();
        $statement->where('entraId', $entraId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatEmployeeId($informatEmployeeId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatEmployeeId', $informatEmployeeId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
