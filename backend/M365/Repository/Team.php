<?php

namespace M365\Repository;

use M365\GraphHelper;
use Ouzo\Utilities\Arrays;
use M365\Interface\Repository;
use Microsoft\Graph\Generated\Models\Team as ModelsTeam;
use Microsoft\Graph\Generated\Models\AadUserConversationMember;
use Microsoft\Graph\Generated\Teams\TeamsRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\Generated\Teams\Item\Channels\ChannelsRequestBuilderGetRequestConfiguration;

class Team extends Repository
{
    public function getByName($name, $select = [])
    {
        $requestConfiguration = new TeamsRequestBuilderGetRequestConfiguration();
        $requestConfiguration->queryParameters = TeamsRequestBuilderGetRequestConfiguration::createQueryParameters();
        $requestConfiguration->queryParameters->filter = "displayName eq '{$name}'";
        if ($select) $requestConfiguration->queryParameters->select = $select;

        return $this->iterate(GraphHelper::$appClient->teams()->get($requestConfiguration)->wait());
    }

    public function getChannels($teamId, $select = [])
    {
        $config = new ChannelsRequestBuilderGetRequestConfiguration();
        $config->queryParameters = ChannelsRequestBuilderGetRequestConfiguration::createQueryParameters();
        if ($select) $config->queryParameters->select = $select;

        return $this->iterate(GraphHelper::$appClient->teams()->byTeamId($teamId)->channels()->get($config)->wait());
    }

    public function getChannelMembers($teamId, $channelId)
    {
        return GraphHelper::$appClient->teams()->byTeamId($teamId)->channels()->byChannelId($channelId)->members()->get()->wait();
    }

    public function removeChannelMember($teamId, $channelId, $conversationMemberId)
    {
        GraphHelper::$appClient->teams()->byTeamId($teamId)->channels()->byChannelId($channelId)->members()->byConversationMemberId($conversationMemberId)->delete()->wait();
    }

    public function addChannelMember($teamId, $channelId, $memberId)
    {
        $requestBody = new AadUserConversationMember();
        $requestBody->setOdataType('#microsoft.graph.aadUserConversationMember');
        $requestBody->setRoles([]);
        $requestBody->setAdditionalData([
            'user@odata.bind' => "https://graph.microsoft.com/v1.0/users/{$memberId}",
        ]);

        GraphHelper::$appClient->teams()->byTeamId($teamId)->channels()->byChannelId($channelId)->members()->post($requestBody)->wait();
    }

    public function setChannelMembers($teamId, $channelId, $members)
    {
        $currentMembers = $this->getChannelMembers($teamId, $channelId)->getValue();
        $currentMembers = Arrays::filter($currentMembers, fn($cm) => !Arrays::contains($cm->getRoles(), "Owner"));

        $removeMembers = $newMembers = [];
        foreach ($currentMembers as $cm) {
            if (!Arrays::contains($members, $cm->getUserId())) $removeMembers[] = $cm->getId();
        }

        foreach ($members as $member) {
            if (!Arrays::contains(Arrays::map($currentMembers, fn($cm) => $cm->getUserId()), $member)) $newMembers[] = $member;
        }

        Arrays::each($removeMembers, fn($rm) => $this->removeChannelMember($teamId, $channelId, $rm));
        Arrays::each($newMembers, fn($nm) => $this->addChannelMember($teamId, $channelId, $nm));
    }

    public function create($name, $templateId = "educationClass", $owner = ["c32b7ef1-64ab-4b0a-bc65-cd4e6da8efae"])
    {
        $team = new ModelsTeam;
        $team->setDisplayName($name);
        $team->setDescription($name);
        $team->setAdditionalData([
            'template@odata.bind' => "https://graph.microsoft.com/v1.0/teamsTemplates('{$templateId}')"
        ]);

        $owners = [];
        foreach ($owner as $own) {
            $_own = new AadUserConversationMember;
            $_own->setOdataType('#microsoft.graph.aadUserConversationMember');
            $_own->setRoles(['owner']);
            $_own->setAdditionalData([
                'user@odata.bind' => "https://graph.microsoft.com/v1.0/users('{$own}')"
            ]);

            $owners[] = $_own;
        }

        $team->setMembers($owners);

        return GraphHelper::$appClient->teams()->post($team)->wait();
    }
}
