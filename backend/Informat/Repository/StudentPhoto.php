<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class StudentPhoto extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_STUDENT_STUDENTS, \Informat\Object\StudentPhoto::class, "photo", shift: "photo");
    }
}
