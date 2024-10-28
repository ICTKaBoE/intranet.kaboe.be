<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Patchpanel extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_patchpanel", \Database\Object\Management\Patchpanel::class, orderField: 'name');
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

    public function getByRoomId($roomId)
    {
        $statement = $this->prepareSelect();
        $statement->where('roomId', $roomId);

        return $this->executeSelect($statement);
    }

    public function getByCabinetId($cabinetId)
    {
        $statement = $this->prepareSelect();
        $statement->where('cabinetId', $cabinetId);

        return $this->executeSelect($statement);
    }
}
