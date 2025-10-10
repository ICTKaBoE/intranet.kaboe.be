<?php

namespace Database\Repository\TempReg;

use Database\Interface\Repository;

class TempReg extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_tempreg", \Database\Object\TempReg\TempReg::class, orderField: 'datetime', orderDirection: self::ORDER_DIRECTION_DESC, guidField: false);
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolIdBetween($schoolId, $start, $end)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        $statement->where('datetime', '>=', $start);
        $statement->where('datetime', '<=', $end);
        return $this->executeSelect($statement);
    }
}
