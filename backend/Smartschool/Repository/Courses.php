<?php

namespace Smartschool\Repository;

use Smartschool\Interface\Repository;

class Courses extends Repository
{
    public function __construct($sourceId)
    {
        parent::__construct($sourceId, "getCourses", \Smartschool\Object\Courses::class, [self::OUTPUT_BASE64, self::OUTPUT_XML, self::OUTPUT_ARRAY], ["course"]);
    }
}
