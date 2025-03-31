<?php

namespace Database\Object\Signage;

use Database\Interface\CustomObject;

class Screen extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "code" => "string",
        "name" => "string",
        "groupId" => "int",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "group" => ['groupId' => \Database\Repository\Signage\Group::class]
    ];
}
