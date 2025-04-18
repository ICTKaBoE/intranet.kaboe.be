<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class StudentRelation extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_student_relation", \Database\Object\Informat\StudentRelation::class, orderField: 'rank', deletedField: false, guidField: 'informatGuid');
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

    public function getByInformatGuid($informatGuid)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatGuid', $informatGuid);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
