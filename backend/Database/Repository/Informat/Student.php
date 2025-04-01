<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Student extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_student", \Database\Object\Informat\Student::class, orderField: 'name', deletedField: false, guidField: 'informatGuid');
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatId', $informatId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatGuid($informatGuid)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatGuid', $informatGuid);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
