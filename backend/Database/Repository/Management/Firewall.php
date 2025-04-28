<?php

namespace Database\Repository\Management;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Firewall extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_management_firewall", \Database\Object\Management\Firewall::class, orderField: 'hostname');
    }

    public function getBySchoolId($schoolId)
    {
        $statement = $this->prepareSelect(filters: ['schoolId' => $schoolId]);
        return $this->executeSelect($statement);
    }

    public function getByBuildingId($buildingId)
    {
        $statement = $this->prepareSelect(filters: ['buildingId' => $buildingId]);
        return $this->executeSelect($statement);
    }

    public function getByRoomId($roomId)
    {
        $statement = $this->prepareSelect(filters: ['roomId' => $roomId]);
        return $this->executeSelect($statement);
    }

    public function getByCabinetId($cabinetId)
    {
        $statement = $this->prepareSelect(filters: ['cabinetId' => $cabinetId]);
        return $this->executeSelect($statement);
    }
}
