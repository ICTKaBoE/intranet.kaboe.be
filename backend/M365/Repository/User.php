<?php

namespace M365\Repository;

use M365\GraphHelper;
use M365\Interface\Repository;
use Microsoft\Graph\Generated\Groups\Item\TransitiveMembers\TransitiveMembersRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Groups\Item\TransitiveMembers\TransitiveMembersRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\Generated\Users\UsersRequestBuilderGetRequestConfiguration;

class User extends Repository
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

    public function getAllEmployees($select = [])
    {
        $config = new  UsersRequestBuilderGetRequestConfiguration;
        $headers = ["ConsistencyLevel" => "eventual"];
        $config->headers = $headers;
        $config->queryParameters = UsersRequestBuilderGetRequestConfiguration::createQueryParameters();
        $config->queryParameters->top = 999;
        $config->queryParameters->count = true;
        $config->queryParameters->filter = "endswith(mail,'@coltd.be') and employeeId ne null";
        if ($select) $config->queryParameters->select = $select;

        return $this->iterate(GraphHelper::$appClient->users()->get($config)->wait());
    }
}
