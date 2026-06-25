<?php

namespace Database\Repository\Informat;

use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;

class ClassGroupTeacher extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_classgroup_teacher", \Database\Object\Informat\ClassgroupTeacher::class, orderField: 'informatClassgroupId', deletedField: false, guidField: false);
    }

    public function getByInformatClassgroupId($informatClassgroupId)
    {
        $statement = $this->prepareSelect(filters: ['informatClassGroupId' => $informatClassgroupId]);
        return $this->executeSelect($statement);
    }

    public function getByInformatEmployeeId($informatEmployeeId)
    {
        $statement = $this->prepareSelect(filters: ['informatEmployeeId' => $informatEmployeeId]);
        return $this->executeSelect($statement);
    }

    public function getByInformatClassgroupIdAndInformatEmployeeId($informatClassgroupId, $informatEmployeeId)
    {
        $statement = $this->prepareSelect(filters: ['informatClassgroupId' => $informatClassgroupId, 'informatEmployeeId' => $informatEmployeeId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
