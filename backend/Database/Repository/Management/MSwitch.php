<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class MSwitch extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_switch", \Database\Object\Management\MSwitch::class, orderField: 'name');
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
