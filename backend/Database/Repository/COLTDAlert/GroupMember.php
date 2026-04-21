<?php

namespace Database\Repository\COLTDAlert;

use Database\Interface\Repository;

class GroupMember extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_coltdalert_group_member", \Database\Object\COLTDAlert\GroupMember::class, orderField: false, deletedField: false, guidField: false);
    }

    public function getByGroupId($groupId)
    {
        $statement = $this->prepareSelect(filters: ['groupId' => $groupId]);
        return $this->executeSelect($statement);
    }
}
