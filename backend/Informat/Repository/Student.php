<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class Student extends Repository
{
    public function __construct()
    {
        parent::__construct(self::ENDPOINT_STUDENT_STUDENTS, \Informat\Object\Student::class, shift: "students");
    }
}
