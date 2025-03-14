<?php

namespace Database\Object\Security;

use Database\Interface\CustomObject;

class GroupUser extends CustomObject
{
    protected $objectAttributes = [
        "securityGroupId" => "int",
        "userId" => "int"
    ];

    protected $linkedAttributes = [
        "securityGroup" => [
            "securityGroupId" => \Database\Repository\Security\Group::class
        ],
        "user" => [
            "userId" => \Database\Repository\User\User::class
        ]
    ];
}
