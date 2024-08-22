<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class EmployeeOwnfield extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_EMPLOYEE, \Informat\Object\EmployeeOwnfield::class, "ownfields", 2);
    }
}
