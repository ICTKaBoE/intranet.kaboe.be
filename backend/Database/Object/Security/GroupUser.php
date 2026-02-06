<?php

namespace Database\Object\Security;

use Security\CustomObject;

class GroupUser extends CustomObject
{
    protected $objectAttributes = [
        "securityGroupId" => self::TYPE_INTEGER,
        "userId" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "securityGroup" => [
            "securityGroupId" => \Database\Repository\Security\Group::class
        ],
    ];
}
