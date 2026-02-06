<?php

namespace Smartschool\Repository;

use Smartschool\Interface\Repository;

class SkoreClassTeacherCourseRelation extends Repository
{
    public function __construct($sourceId)
    {
        parent::__construct($sourceId, "getSkoreClassTeacherCourseRelation", \Smartschool\Object\SkoreClassTeacherCourseRelation::class, [self::OUTPUT_XML, self::OUTPUT_ARRAY], ["courseTeacherClass"]);
    }
}
