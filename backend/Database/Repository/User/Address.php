<?php

namespace Database\Repository\User;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Address extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_user_address", \Database\Object\User\Address::class, orderField: "since", guidField: false);
    }

    public function getCurrentByUserId($userId)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);
        $statement->where('current', 1);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);

        return $this->executeSelect($statement);
    }

    public function getByInformatEmployeeAddressId($informatEmployeeAddressId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatEmployeeAddressId', $informatEmployeeAddressId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
