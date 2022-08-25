<?php

namespace O365\Objects;

use Microsoft\Graph\Model\User as ModelUser;
use O365\Interfaces\CustomObject;

class User extends CustomObject
{
    protected $selectProperties = [
        // 'aboutMe',
        'accountEnabled',
        'ageGroup',
        'assignedLicenses',
        'assignedPlans',
        // 'birthday',
        'businessPhones',
        'city',
        'companyName',
        'consentProvidedForMinor',
        'country',
        'createdDateTime',
        'creationType',
        'deletedDateTime',
        'department',
        'displayName',
        'employeeHireDate',
        'employeeId',
        'employeeOrgData',
        'employeeType',
        'externalUserState',
        'externalUserStateChangeDateTime',
        'faxNumber',
        'givenName',
        // 'hireDate',
        'id',
        'identities',
        'imAddresses',
        // 'interests',
        'isResourceAccount',
        'jobTitle',
        'lastPasswordChangeDateTime',
        'legalAgeGroupClassification',
        'licenseAssignmentStates',
        'mail',
        // 'mailboxSettings',
        // 'mailNickname',
        'mobilePhone',
        // 'mySite',
        'officeLocation',
        'onPremisesDistinguishedName',
        'onPremisesDomainName',
        'onPremisesExtensionAttributes',
        'onPremisesImmutableId',
        'onPremisesLastSyncDateTime',
        'onPremisesProvisioningErrors',
        'onPremisesSamAccountName',
        'onPremisesSecurityIdentifier',
        'onPremisesSyncEnabled',
        'onPremisesUserPrincipalName',
        // 'otherMails',
        'passwordPolicies',
        'passwordProfile',
        // 'pastProjects',
        'postalCode',
        // 'preferredDataLocation',
        // 'preferredLanguage',
        // 'preferredName',
        'provisionedPlans',
        'proxyAddresses',
        'refreshTokenValidFromDateTime',
        // 'responsibilities',
        // 'schools',
        // 'showInAddressList',
        // 'skills',
        'signInSessionValidFromDateTime',
        'state',
        'streetAddress',
        'surname',
        'usageLocation',
        'userPrincipalName',
        'userType'
    ];

    public function __construct()
    {
        parent::__construct("/users", ModelUser::class);
    }

    public function get($oid)
    {
        return $this->createRequest()
            ->setOid($oid)
            ->setSelect()
            ->doRequest();
    }
}
