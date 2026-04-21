<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class EmployeeNumber extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_employee_number", \Database\Object\Informat\EmployeeNumber::class, orderField: 'type', deletedField: false, guidField: 'informatGuid');
    }

    public function getByInformatEmployeeId($informatEmployeeId)
    {
        $statement = $this->prepareSelect(filters: ['informatEmployeeId' => $informatEmployeeId]);
        return $this->executeSelect($statement);
    }

    public function getByInformatGuid($informatGuid)
    {
        $statement = $this->prepareSelect(filters: ['informatGuid' => $informatGuid]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByCategory($category)
    {
        $statement = $this->prepareSelect(filters: ['category' => $category]);
        return $this->executeSelect($statement);
    }
}
