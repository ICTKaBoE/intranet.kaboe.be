<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class SecurityGroupUser extends CustomObject
{
    protected $objectAttributes = [
        "securityGroupId" => "int",
        "userId" => "int"
    ];

    protected $linkedAttributes = [
        "securityGroup" => [
            "securityGroupId" => \Database\Repository\SecurityGroup::class
        ],
        "user" => [
            "userId" => \Database\Repository\User::class
        ]
    ];
}
