<?php

namespace Database\Repository\Accident;

use Database\Interface\Repository;

class Accident extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_accident", \Database\Object\Accident\Accident::class, orderField: 'creationDateTime', orderDirection: self::ORDER_DIRECTION_DESC);
    }

    public function getByCreatorUserId($creatorUserId)
    {
        $statement = $this->prepareSelect(filters: ['creatorUserId' => $creatorUserId]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        return $this->executeSelect($statement);
    }
}
