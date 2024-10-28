<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Teacher extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_teacher", \Database\Object\Informat\Teacher::class, orderField: 'name', deletedField: false, guidField: false);
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatId', $informatId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getBySchoolyear($schoolyear)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolyear', $schoolyear);

        return $this->executeSelect($statement);
    }

    public function getByNotSchoolyear($schoolyear)
    {
        $statement = $this->prepareSelect();
        $statement->whereNot('schoolyear', $schoolyear);

        return $this->executeSelect($statement);
    }
}
