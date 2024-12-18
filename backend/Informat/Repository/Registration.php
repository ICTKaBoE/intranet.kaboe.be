<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class Registration extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_STUDENT_REGISTRATIONS, \Informat\Object\Registration::class, shift: "registrations");
    }
}
