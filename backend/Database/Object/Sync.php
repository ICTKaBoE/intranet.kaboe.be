<?php

namespace Database\Object;

use Helpers\HTML;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;
use Database\Repository\Informat\Student;
use Database\Repository\Informat\Employee;
use Ouzo\Utilities\Clock;

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
        "thumbnailPhoto" => "string",
        "setEmail" => "string",
        "setPassword" => "string",
        "lastAction" => "string",
        "lastError" => "string",
        "lastSync" => "datetime"
    ];

    public function init()
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $this->linked->employee = ($this->type == "E" ? (new Employee)->getByInformatId($this->employeeId) : (new Student)->getByInformatId($this->employeeId));

        $this->formatted->badge->nextAction = HTML::Badge($settings['action'][$this->action]["name"] ?: "N/A", backgroundColor: $settings['action'][$this->action]["color"] ?: "secondary");
        $this->formatted->badge->lastAction = HTML::Badge($settings['action'][$this->lastAction]["name"] ?: "N/A", backgroundColor: $settings['action'][$this->lastAction]["color"] ?: "secondary");

        $this->formatted->lastSyncWithError = is_null($this->lastSync) ? null : Clock::at($this->lastSync)->format("d/m/Y H:i:s") . ($this->lastError ? " ({$this->lastError})" : "");
    }

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
            is_null($this->ou) &&
            is_null($this->thumbnailPhoto)
        );
    }
}
