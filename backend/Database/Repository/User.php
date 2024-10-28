<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

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

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatId', $informatId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByEmployeeId($employeeId)
    {
        $statement = $this->prepareSelect();
        $statement->where('employeeId', $employeeId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
