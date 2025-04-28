<?php

namespace Database\Repository\Helpdesk;

use Database\Interface\Repository;

class Ticket extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_helpdesk_ticket", \Database\Object\Helpdesk\Ticket::class, orderField: 'lastActionDateTime');
    }

    public function getByCreatorUserId($creatorUserId)
    {
        $statement = $this->prepareSelect(filters: [
            'creatorUserId' => $creatorUserId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByCreatorUserIdStatusAndSchoolId($creatorUserId, $status, $schoolId)
    {
        $statement = $this->prepareSelect(filters: [
            'creatorUserId' => $creatorUserId,
            'status' => $status,
            'schoolId' => $schoolId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByAssignedToUserId($assignedToUserId)
    {
        $statement = $this->prepareSelect(filters: [
            'assignedToUserId' => $assignedToUserId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByAssignedToUserIdStatusAndSchoolId($assignedToUserId, $status, $schoolId)
    {
        $statement = $this->prepareSelect(filters: [
            'assignedToUserId' => $assignedToUserId,
            'status' => $status,
            'schoolId' => $schoolId
        ]);

        return $this->executeSelect($statement);
    }

    public function getByMainCategoryAndAssetId($mainCategory, $assetId)
    {
        $statement = $this->prepareSelect(filters: [
            'assetId' => $assetId
        ])
            ->where("category", "LIKE", "{$mainCategory}%");

        return $this->executeSelect($statement);
    }

    public function getByStatusAndSchoolId($status, $schoolId)
    {
        $statement = $this->prepareSelect([
            'status' => $status,
            'schoolId' => $schoolId
        ]);

        return $this->executeSelect($statement);
    }
}
