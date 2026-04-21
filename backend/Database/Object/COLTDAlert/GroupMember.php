<?php

namespace Database\Object\COLTDAlert;

use Security\CustomObject;

class GroupMember extends CustomObject
{
    protected $objectAttributes = [
        "groupId" => self::TYPE_INTEGER,
        "informatEmployeeNumberId" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "informatEmployeeNumber" => ["informatEmployeeNumberId" => \Database\Repository\Informat\EmployeeNumber::class]
    ];
}
