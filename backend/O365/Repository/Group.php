<?php

namespace O365\Repository;

use Microsoft\Graph\Model\Group as ModelGroup;
use O365\Interfaces\Repository;

class Group extends Repository
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
            ->setSelect(['displayName', 'id'])
            ->setFilter("displayName eq '{$displayName}'");
    }

    public function getMembersByGroupId($id)
    {
        return $this->createRequest()
            ->setSelect(['companyName', 'givenName', 'id', 'jobTitle', 'mail', 'surname', 'accountEnabled'])
            ->setOid($id)
            ->getMembers();
    }
}
