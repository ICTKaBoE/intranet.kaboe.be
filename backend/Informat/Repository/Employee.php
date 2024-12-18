<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class Employee extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_EMPLOYEE, \Informat\Object\Employee::class, apiVersion: 2);
    }
}
