<?php

namespace Database\Repository\Informat;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class StudentConfig extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_informat_student_config", \Database\Object\Informat\StudentConfig::class, orderField: false, deletedField: false, guidField: false);
    }

    public function getBySchoolyearIdAndInformatStudentId($schoolyearId, $informatStudentId)
    {
        $statement = $this->prepareSelect(filters: ['schoolyearId' => $schoolyearId, 'informatStudentId' => $informatStudentId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatStudentId($informatStudentId)
    {
        $statement = $this->prepareSelect(filters: [
            'informatStudentId' => $informatStudentId
        ]);

        return $this->executeSelect($statement);
    }

    public function getBySchoolyearId($schoolyearId)
    {
        $statement = $this->prepareSelect(filters: [
            'schoolyearId' => $schoolyearId
        ]);

        return $this->executeSelect($statement);
    }

    public function getActiveBySchoolyearIdAndClassgroupId($schoolyearId, $classgroupId)
    {
        $statement = $this->prepareSelect(filters: [
            'schoolyearId' => $schoolyearId,
            'classgroupId' => $classgroupId,
            'active' => true
        ]);

        return $this->executeSelect($statement);
    }
}
