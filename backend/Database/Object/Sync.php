<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Strings;

class Sync extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "type" => "string",
        "action" => "string",
        "employeeId" => "int",
        "givenName" => "string",
        "surname" => "string",
        "displayName" => "string",
        "emailAddress" => "string",
        "userPrincipalName" => "string",
        "samAccountName" => "string",
        "companyName" => "string",
        "department" => "string",
        "jobTitle" => "string",
        "memberOf" => "json",
        "otherAttributes" => "json",
        "password" => "string",
        "ou" => "string",
        "setEmail" => "string",
        "setPassword" => "string",
        "lastAction" => "string",
        "lastError" => "string",
        "lastSync" => "datetime"
    ];

    public function noUpdate()
    {
        return (
            is_null($this->givenName) &&
            is_null($this->surname) &&
            is_null($this->displayName) &&
            is_null($this->emailAddress) &&
            is_null($this->userPrincipalName) &&
            is_null($this->samAccountName) &&
            is_null($this->companyName) &&
            is_null($this->department) &&
            is_null($this->jobTitle) &&
            is_null($this->memberOf) &&
            is_null($this->password) &&
            is_null($this->ou)
        );
    }
}
