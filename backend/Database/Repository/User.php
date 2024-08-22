<?php

namespace Database\Repository;

use Database\Interface\Repository;

class User extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_user", \Database\Object\User::class, orderField: "name");
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

        return $this->executeSelect($statement);
    }
}
