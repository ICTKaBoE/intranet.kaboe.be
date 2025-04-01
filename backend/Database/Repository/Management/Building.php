<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Building extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_building", \Database\Object\Management\Building::class, orderField: 'name');
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);

        return $this->executeSelect($statement);
    }
}
