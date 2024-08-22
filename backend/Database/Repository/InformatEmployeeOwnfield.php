<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class InformatEmployeeOwnfield extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_employee_ownfield", \Database\Object\InformatEmployeeOwnfield::class, orderField: 'name', deletedField: false);
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatId', $informatId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatIdAndInformatEmployeeId($informatId, $informatEmployeeId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatId', $informatId);
        $statement->where('informatEmployeeId', $informatEmployeeId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
