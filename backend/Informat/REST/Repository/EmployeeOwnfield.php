<?php

namespace Informat\REST\Repository;

use Informat\REST\Interface\Repository;

class EmployeeOwnfield extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_EMPLOYEE, \Informat\REST\Object\EmployeeOwnfield::class, "ownfields", 2);
    }
}
