<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Employee extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_employee", \Database\Object\Informat\Employee::class, orderField: 'name', deletedField: false, guidField: 'informatGuid');
    }

    public function getActive()
    {
        $statement = $this->prepareSelect(filters: ['active' => true]);
        return $this->executeSelect($statement);
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect(filters: ['informatId' => $informatId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatGuid($informatGuid)
    {
        $statement = $this->prepareSelect(filters: ['informatGuid' => $informatGuid]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
