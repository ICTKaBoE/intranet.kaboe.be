<?php

namespace O365\Objects;

use Microsoft\Graph\Model\Group as ModelGroup;
use Microsoft\Graph\Model\User as ModelUser;
use O365\Interfaces\CustomObject;

class Group extends CustomObject
{
    protected $selectProperties = [
        // "allowExternalSenders",
        "assignedLabels",
        "assignedLicenses",
        "autoSubscribeMembers",
        "classification",
        "createdDateTime",
        "deletedDateTime",
        "description",
        "displayName",
        "expirationDateTime",
        "groupTypes",
        // "hasMembersWithLicenseErrors",
        // "hideFromAddressLists",
        // "hideFromOutlookClients",
        "id",
        "isAssignableToRole",
        // "isSubscribedByMail",
        "licenseProcessingState",
        "mail",
        "mailEnabled",
        "mailNickname",
        "membershipRule",
        "membershipRuleProcessingState",
        "onPremisesLastSyncDateTime",
        "onPremisesProvisioningErrors",
        "onPremisesSamAccountName",
        "onPremisesSecurityIdentifier",
        "onPremisesSyncEnabled",
        "preferredDataLocation",
        "preferredLanguage",
        "proxyAddresses",
        "renewedDateTime",
        "resourceBehaviorOptions",
        "resourceProvisioningOptions",
        "securityEnabled",
        "securityIdentifier",
        "theme",
        // "unseenCount",
        "visibility"
    ];

    public function __construct()
    {
        parent::__construct("/groups", ModelGroup::class);
    }

    public function getByDisplayName($displayName)
    {
        return $this->createRequest()
            ->setSelect(['displayName', 'id', 'mail'])
            ->setFilter("displayName eq '{$displayName}'");
    }

    public function getMembersById($id)
    {
        return $this->createRequest()
            ->setSelect(['displayName', 'id', 'mail'])
            ->setOid($id)
            ->getMembers();
    }
}
