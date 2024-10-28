<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class TeacherFreefield extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_teacher_freefield", \Database\Object\Informat\TeacherFreefield::class, orderField: 'description', deletedField: false, guidField: false);
    }

    public function getByInformatTeacherId($informatTeacherId)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatTeacherId', $informatTeacherId);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatTeacherIdSesionAndDescription($informatTeacherId, $section, $description)
    {
        $statement = $this->prepareSelect();
        $statement->where('informatTeacherId', $informatTeacherId);
        $statement->where('section', $section);
        $statement->where('description', $description);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
