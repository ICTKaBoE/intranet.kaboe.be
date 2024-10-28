<?php

namespace Informat\REST\Repository;

use Informat\REST\Interface\Repository;

class Employee extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_EMPLOYEE, \Informat\REST\Object\Employee::class, apiVersion: 2);
    }
}
