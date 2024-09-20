<?php

namespace Database\Repository;

use Database\Interface\Repository;

class BikeDistance extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_bike_distance", \Database\Object\BikeDistance::class, orderField: 'distance');
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
