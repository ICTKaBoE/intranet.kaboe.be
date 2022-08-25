<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class User extends CustomObject
{
    protected $objectAttributes = [
        'id',
        'username',
        'password',
        'displayName',
        'jobTitle',
        'companyName',
        'enabled',
        'deleted'
    ];

    public function getMail()
    {
        return $this->username;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    public function getCompanyName()
    {
        return $this->companyName;
    }
}
