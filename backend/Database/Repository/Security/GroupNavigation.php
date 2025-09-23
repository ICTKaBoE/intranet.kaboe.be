<?php

namespace Database\Repository\Security;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class GroupNavigation extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_security_group_navigation", \Database\Object\Security\GroupNavigation::class, orderField: false, deletedField: false, guidField: false);
    }

    public function getByNavigationId($navigationId)
    {
        $statement = $this->prepareSelect(filters: ['navigationId' => $navigationId]);
        return $this->executeSelect($statement);
    }

    public function getBySecurityGroupId($securityGroupId)
    {
        $statement = $this->prepareSelect(filters: ['securityGroupId' => $securityGroupId]);
        return $this->executeSelect($statement);
    }

    public function getBySecurityGroupIdAndNavigationId($securityGroupId, $navigationId)
    {
        $statement = $this->prepareSelect(filters: [
            'securityGroupId' => $securityGroupId,
            'navigationId' => $navigationId
        ]);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
