<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class StudentEmail extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_student_email", \Database\Object\Informat\StudentEmail::class, orderField: 'type', deletedField: false, guidField: false);
    }

    public function getByInformatStudentId($informatStudentId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatStudentId', $informatStudentId);

        return $this->executeSelect($statement);
    }

    public function getByInformatId($informatId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatId', $informatId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
