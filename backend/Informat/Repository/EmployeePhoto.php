<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class EmployeePhoto extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_EMPLOYEE, \Informat\Object\EmployeePhoto::class, "photos", apiVersion: 2);
    }
}
