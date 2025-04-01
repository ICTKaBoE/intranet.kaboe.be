<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Sync extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_sync", \Database\Object\Sync::class, orderField: 'lastSync', orderDirection: 'DESC', deletedField: false, guidField: false);
    }

    public function getByEmployeeId($employeeId)
    {
        $statement = $this->prepareSelect();
        $statement->where('employeeId', $employeeId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
