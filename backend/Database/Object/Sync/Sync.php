<?php

namespace Database\Object\Sync;

use Helpers\HTML;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Database\Repository\Navigation\Navigation;
use Security\CustomObject;
use Database\Repository\Informat\Student;
use Database\Repository\Informat\Employee;
use Database\Repository\Sync\Action;
use Ouzo\Utilities\Clock;

class Sync extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "type" => self::TYPE_STRING,
        "action" => self::TYPE_STRING,
        "employeeId" => self::TYPE_INTEGER,
        "givenName" => self::TYPE_STRING,
        "surname" => self::TYPE_STRING,
        "displayName" => self::TYPE_STRING,
        "emailAddress" => self::TYPE_STRING,
        "userPrincipalName" => self::TYPE_STRING,
        "samAccountName" => self::TYPE_STRING,
        "companyName" => self::TYPE_STRING,
        "department" => self::TYPE_STRING,
        "jobTitle" => self::TYPE_STRING,
        "memberOf" => self::TYPE_JSON,
        "otherAttributes" => self::TYPE_JSON,
        "password" => self::TYPE_STRING,
        "ou" => self::TYPE_STRING,
        "thumbnailPhoto" => self::TYPE_STRING,
        "setEmail" => self::TYPE_STRING,
        "setPassword" => self::TYPE_STRING,
        "lastAction" => self::TYPE_STRING,
        "lastError" => self::TYPE_STRING,
        "lastSync" => self::TYPE_DATETIME
    ];

    public function init()
    {
        $actionRepo = new Action;
        $this->linked->employee = ($this->type == "E" ? (new Employee)->getByInformatId($this->employeeId) : (new Student)->getByInformatId($this->employeeId));

        $nextAction = $actionRepo->getById($this->action);
        $lastAction = $actionRepo->getById($this->lastAction);
        $this->formatted->badge->nextAction = HTML::Badge($nextAction->name ?: "N/A", backgroundColor: $nextAction->color ?: "secondary");
        $this->formatted->badge->lastAction = HTML::Badge($lastAction->name ?: "N/A", backgroundColor: $lastAction->color ?: "secondary");

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
            is_null($this->otherAttributes) &&
            is_null($this->password) &&
            is_null($this->ou) &&
            is_null($this->thumbnailPhoto)
        );
    }
}
