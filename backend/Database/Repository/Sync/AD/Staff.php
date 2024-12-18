<?php

namespace Database\Repository\Sync\AD;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Staff extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_sync_ad_staff", \Database\Object\Sync\AD\Staff::class, orderField: false, deletedField: false, guidField: false);
    }

    public function getByEmployeeId($employeeId)
    {
        $statement = $this->prepareSelect();
        $statement->where('employeeId', $employeeId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
