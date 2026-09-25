<?php

namespace Database\Repository\Remedy;

use Database\Interface\Repository;

class Remedy extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_remedy", \Database\Object\Remedy\Remedy::class, orderField: "id", orderDirection: self::ORDER_DIRECTION_DESC);
    }

    public function getByMomentId($momentId)
    {
        $statement = $this->prepareSelect(filters: [
            'momentId' => $momentId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByTypeId($typeId)
    {
        $statement = $this->prepareSelect(filters: [
            'typeId' => $typeId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByAssignedDate($assignedDate)
    {
        $statement = $this->prepareSelect(filters: [
            'assignedDate' => $assignedDate
        ]);

        return $this->executeSelect($statement);
    }

    public function getByMomentIdAndAssignedDate($momentId, $assignedDate)
    {

        $statement = $this->prepareSelect(filters: [
            'momentId' => $momentId,
            'assignedDate' => $assignedDate
        ]);

        return $this->executeSelect($statement);
    }

    public function getMultipleByInformatStudentIdTypeIdAndMomentId($informatStudentId, $typeId, $momentId)
    {
        $statement = $this->prepareSelect(filters: [
            'informatStudentId' => $informatStudentId,
            'typeId' => $typeId,
            'momentId' => $momentId
        ]);

        return $this->executeSelect($statement);
    }

    public function getMultipleByInformatStudentIdTypeIdMomentIdAndCourseId($informatStudentId, $typeId, $momentId, $courseId)
    {
        $statement = $this->prepareSelect(filters: [
            'informatStudentId' => $informatStudentId,
            'typeId' => $typeId,
            'momentId' => $momentId,
            'courseId' => $courseId
        ]);

        return $this->executeSelect($statement);
    }
}
