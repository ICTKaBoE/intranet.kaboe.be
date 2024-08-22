<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class InformatEmployeeNumber extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_employee_number", \Database\Object\InformatEmployeeNumber::class, orderField: 'number', deletedField: false);
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatId', $informatId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatEmployeeId($informatEmployeeId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatEmployeeId', $informatEmployeeId);

        return $this->executeSelect($statement);
    }
}
