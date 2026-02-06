<?php

namespace Database\Repository\School;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class CourseInformatEmployeeClassgroup extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_course_informat_employee_classgroup", \Database\Object\School\CourseInformatEmployeeClassgroup::class, orderField: false, deletedField: false, guidField: false);
    }

    public function getBySchoolCourseId($schoolCourseId)
    {
        $statement = $this->prepareSelect(filters: ['schoolCourseId' => $schoolCourseId]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolCourseIdAndInformatClassgroupId($schoolCourseId, $informatClassgroupId)
    {
        $statement = $this->prepareSelect(filters: ['schoolCourseId' => $schoolCourseId, "informatClassgroupId" => $informatClassgroupId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }

    public function getByInformatEmployeeId($informatEmployeeId)
    {
        $statement = $this->prepareSelect(filters: ['informatEmployeeId' => $informatEmployeeId]);
        return $this->executeSelect($statement);
    }

    public function getByInformatClassgroupId($informatClassgroupId)
    {
        $statement = $this->prepareSelect(filters: ['informatClassgroupId' => $informatClassgroupId]);
        return $this->executeSelect($statement);
    }

    public function getByType($type)
    {
        $statement = $this->prepareSelect(filters: ['type' => $type]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolCourseIdInformatEmployeeIdAndInformatClassgroupId($schoolCourseId, $informatEmployeeId, $informatClassgroupId)
    {
        $statement = $this->prepareSelect(filters: ['schoolCourseId' => $schoolCourseId, 'informatEmployeeId' => $informatEmployeeId, 'informatClassgroupId' => $informatClassgroupId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
