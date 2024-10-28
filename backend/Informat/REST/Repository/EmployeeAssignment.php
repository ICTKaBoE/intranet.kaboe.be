<?php

namespace Informat\REST\Repository;

use Informat\REST\Interface\Repository;

class EmployeeAssignment extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_EMPLOYEE, \Informat\REST\Object\EmployeeAssignment::class, "assignments", 2);
    }
}
