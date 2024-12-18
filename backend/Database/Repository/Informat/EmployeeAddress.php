<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class EmployeeAddress extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_employee_address", \Database\Object\Informat\EmployeeAddress::class, orderField: 'street', deletedField: false, guidField: 'informatGuid');
    }

    public function getByInformatEmployeeId($informatEmployeeId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatEmployeeId', $informatEmployeeId);

        return $this->executeSelect($statement);
    }

    public function getByInformatGuid($informatGuid)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatGuid', $informatGuid);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
