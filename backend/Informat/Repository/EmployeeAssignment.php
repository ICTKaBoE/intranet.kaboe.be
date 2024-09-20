<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class EmployeeAssignment extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_EMPLOYEE, \Informat\Object\EmployeeAssignment::class, "assignments", 2);
    }
}
