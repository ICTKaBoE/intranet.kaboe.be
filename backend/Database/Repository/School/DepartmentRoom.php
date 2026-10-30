<?php

namespace Database\Repository\School;

use Database\Interface\Repository;

class DepartmentRoom extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_department_room", \Database\Object\School\DepartmentRoom::class, orderField: "roomId", deletedField: false);
    }

    public function getByDepartmentId($departmentId)
    {
        $statement = $this->prepareSelect(filters: ['departmentId' => $departmentId]);
        return $this->executeSelect($statement);
    }
}
