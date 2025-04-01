<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Room extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_room", \Database\Object\Management\Room::class, orderField: 'floor');
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
