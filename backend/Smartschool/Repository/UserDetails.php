<?php

namespace Smartschool\Repository;

use Smartschool\Interface\Repository;

class UserDetails extends Repository
{
    public function __construct($sourceId)
    {
        parent::__construct($sourceId, "getUserDetails", \Smartschool\Object\UserDetails::class, self::OUTPUT_JSON);
    }
}
