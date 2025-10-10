<?php

namespace Database\Repository\IWE;

use Database\Interface\Repository;

class IWE extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_iwe", \Database\Object\IWE\IWE::class, orderField: 'creationDateTime', orderDirection: self::ORDER_DIRECTION_DESC);
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        return $this->executeSelect($statement);
    }
}
