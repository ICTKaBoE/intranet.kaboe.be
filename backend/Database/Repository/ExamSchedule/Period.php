<?php

namespace Database\Repository\ExamSchedule;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Period extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_examschedule_period", \Database\Object\ExamSchedule\Period::class, orderField: 'name', deletedField: false);
    }
}
