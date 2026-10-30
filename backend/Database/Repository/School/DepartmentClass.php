<?php

namespace Database\Repository\School;

use Database\Interface\Repository;

class DepartmentClass extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_department_class", \Database\Object\School\DepartmentClass::class, orderField: "classgroupId", deletedField: false);
    }

    public function getByDepartmentId($departmentId)
    {
        $statement = $this->prepareSelect(filters: ['departmentId' => $departmentId]);
        return $this->executeSelect($statement);
    }
}
