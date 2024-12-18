<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class StudentBank extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_student_bank", \Database\Object\Informat\StudentBank::class, orderField: 'type', deletedField: false, guidField: false);
    }

    public function getByInformatStudentId($informatStudentId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatStudentId', $informatStudentId);

        return $this->executeSelect($statement);
    }

    public function getByInformatStudentIdAndIban($informatStudentId, $iban)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatStudentId', $informatStudentId);
        $statement->where('iban', $iban);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
