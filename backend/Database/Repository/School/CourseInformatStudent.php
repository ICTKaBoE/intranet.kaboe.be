<?php

namespace Database\Repository\School;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class CourseInformatStudent extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_school_course_informat_student", \Database\Object\School\CourseInformatStudent::class, orderField: false, deletedField: false, guidField: false);
    }

    public function getBySchoolCourseId($schoolCourseId)
    {
        $statement = $this->prepareSelect(filters: ['schoolCourseId' => $schoolCourseId]);
        return $this->executeSelect($statement);
    }

    public function getByInformatStudentId($informatStudentId)
    {
        $statement = $this->prepareSelect(filters: ['informatStudentId' => $informatStudentId]);
        return $this->executeSelect($statement);
    }

    public function getBySchoolCourseIdAndInformatStudentId($schoolCourseId, $informatStudentId)
    {
        $statement = $this->prepareSelect(filters: ['schoolCourseId' => $schoolCourseId, 'informatStudentId' => $informatStudentId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
