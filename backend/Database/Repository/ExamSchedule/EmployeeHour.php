<?php

namespace Database\Repository\ExamSchedule;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class EmployeeHour extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_examschedule_employee_hour", \Database\Object\ExamSchedule\EmployeeHour::class, orderField: false, deletedField: false);
    }

    public function getByInformatEmployeeIdAndSchoolyearId($informatEmployeeId, $schoolyearId)
    {
        $statement = $this->prepareSelect(filters: ['informatEmployeeId' => $informatEmployeeId, 'schoolyearId' => $schoolyearId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
