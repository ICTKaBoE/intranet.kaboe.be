<?php

namespace M365\Repository;

use M365\GraphHelper;
use Ouzo\Utilities\Arrays;
use M365\Interface\Repository;
use Microsoft\Graph\Generated\Models\ReferenceCreate;
use Microsoft\Graph\Generated\Groups\GroupsRequestBuilderGetRequestConfiguration;

class Group extends Repository
{
    public function getByName($name, $select = [])
    {
        $request = new GroupsRequestBuilderGetRequestConfiguration;
        $request->queryParameters = GroupsRequestBuilderGetRequestConfiguration::createQueryParameters();
        $request->queryParameters->filter = "displayName eq '{$name}'";
        if ($select) $request->queryParameters->select = $select;

        return $this->iterate(GraphHelper::$appClient->groups()->get($request)->wait());
    }

    public function convertToDynamicMembership($groupId, $rule, $state = "On")
    {
        $group = GraphHelper::$appClient->groups()->byGroupId($groupId)->get()->wait();
        $group->setMailEnabled(false);
        $group->setGroupTypes(["Unified", "DynamicMembership"]);
        $group->setMembershipRule($rule);
        $group->setMembershipRuleProcessingState($state);

        return GraphHelper::$appClient->groups()->byGroupId($groupId)->patch($group)->wait();
    }

    public function getMembers($groupId)
    {
        return GraphHelper::$appClient->groups()->byGroupId($groupId)->members()->get()->wait();
    }

    public function getOwners($groupId)
    {
        return GraphHelper::$appClient->groups()->byGroupId($groupId)->owners()->get()->wait();
    }

    public function addOwner($groupId, $ownerId)
    {
        $requestBody = new ReferenceCreate();
        $requestBody->setOdataId("https://graph.microsoft.com/v1.0/users/{$ownerId}");

        GraphHelper::$appClient->groups()->byGroupId($groupId)->owners()->ref()->post($requestBody)->wait();
    }

    public function removeOwner($groupId, $ownerId)
    {
        GraphHelper::$appClient->groups()->byGroupId($groupId)->owners()->byDirectoryObjectId($ownerId)->ref()->delete()->wait();
    }

    public function setOwners($groupId, $owners, $mode = "replace")
    {
        $currentOwners = $this->getOwners($groupId)->getValue();
        $currentOwners = Arrays::map($currentOwners, fn($co) => $co->getId());

        if ($mode == "replace") {
            $removeOwners = Arrays::filter($currentOwners, fn($co) => !Arrays::contains($owners, $co));
            Arrays::each($removeOwners, fn($ro) => $this->removeOwner($groupId, $ro));
        }

        $newOwners = Arrays::filter($owners, fn($o) => !Arrays::contains($currentOwners, $o));
        Arrays::each($newOwners, fn($no) => $this->addOwner($groupId, $no));
    }
}
