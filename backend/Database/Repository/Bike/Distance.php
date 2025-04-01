<?php

namespace Database\Repository\Bike;

use Database\Interface\Repository;

class Distance extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_bike_distance", \Database\Object\Bike\Distance::class, orderField: 'distance');
    }

    public function getByUserId($userId)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);

        return $this->executeSelect($statement);
    }

    public function getByUserIdAndType($userId, $type)
    {
        $statement = $this->prepareSelect();
        $statement->where('userId', $userId);
        $statement->where('type', $type);

        return $this->executeSelect($statement);
    }
}
