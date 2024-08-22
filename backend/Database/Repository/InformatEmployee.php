<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class InformatEmployee extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_employee", \Database\Object\InformatEmployee::class, orderField: 'name', deletedField: false);
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatId', $informatId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
