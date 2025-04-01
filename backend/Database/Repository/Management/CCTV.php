<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class CCTV extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_cctv", \Database\Object\Management\CCTV::class, orderField: 'name');
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolId', $schoolId);

        return $this->executeSelect($statement);
    }

    public function getByBuildingId($buildingId)
    {
        $statement = $this->prepareSelect();
        $statement->where('buildingId', $buildingId);

        return $this->executeSelect($statement);
    }
}
