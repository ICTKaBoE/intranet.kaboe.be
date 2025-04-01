<?php

namespace Database\Repository;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;

class TempReg extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_tempreg", \Database\Object\TempReg::class, orderField: 'datetime', orderDirection: 'DESC', guidField: false);
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);

        return $this->executeSelect($statement);
    }
}
