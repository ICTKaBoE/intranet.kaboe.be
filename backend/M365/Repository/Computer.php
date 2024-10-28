<?php

namespace M365\Repository;

use M365\GraphHelper;
use M365\Interface\Repository;
use Microsoft\Graph\Generated\Groups\Item\TransitiveMembers\TransitiveMembersRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Groups\Item\TransitiveMembers\TransitiveMembersRequestBuilderGetRequestConfiguration;

class Computer extends Repository
{
    public function getGroupMembersByGroupId($groupId, $select = [])
    {
        $config = new TransitiveMembersRequestBuilderGetRequestConfiguration;
        $headers = ["ConsistencyLevel" => "eventual"];
        $config->headers = $headers;
        $config->queryParameters = new TransitiveMembersRequestBuilderGetQueryParameters;
        $config->queryParameters->top = 999;
        if ($select) $config->queryParameters->select = $select;

        return $this->iterate(GraphHelper::$appClient->groups()->byGroupId($groupId)->transitiveMembers()->get($config)->wait());
    }
}
