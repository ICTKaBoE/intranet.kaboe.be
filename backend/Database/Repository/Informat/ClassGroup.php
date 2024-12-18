<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class ClassGroup extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_classgroup", \Database\Object\Informat\ClassGroup::class, orderField: 'name', deletedField: false, guidField: 'informatGuid');
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

    public function getBySchoolInstituteId($schoolInstituteId)
    {
        $statement = $this->prepareSelect();
        $statement->where('schoolInstituteId', $schoolInstituteId);

        return $this->executeSelect($statement);
    }
}
