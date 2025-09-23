<?php

namespace Database\Repository\Registration;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Contact extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_registration_contact", \Database\Object\Registration\Contact::class, orderField: "followNumber");
    }

    public function getByRegistrationId($registrationId)
    {
        $statement = $this->prepareSelect(filters: ['registrationId' => $registrationId]);
        return $this->executeSelect($statement);
    }
}
