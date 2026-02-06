<?php

namespace Smartschool\Object;

use Security\CustomObject;

class SkoreClassTeacherCourseRelation extends CustomObject
{
    protected $objectAttributes = [
        "naam" => self::TYPE_STRING,
        "voornaam" => self::TYPE_STRING,
        "gebruikersnaam" => self::TYPE_STRING,
        "internnummer" => self::TYPE_STRING,
        "stamboeknummer" => self::TYPE_STRING,
        "koppelingsveldschoolagenda" => self::TYPE_STRING,
        "klasnaam" => self::TYPE_STRING,
        "vaknaam" => self::TYPE_STRING,
        "vaknaamkort" => self::TYPE_ALL,
        "leerlingen" => [
            "type" => self::TYPE_ALL,
            "sub" => ["leerling" => self::TYPE_ARRAY]
        ]
    ];
}
