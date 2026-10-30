<?php

namespace Database\Repository\Student;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Document extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_student_document", \Database\Object\Student\Document::class, orderField: "alias");
    }
}
