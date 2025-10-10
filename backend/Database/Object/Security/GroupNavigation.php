<?php

namespace Database\Object\Security;

use Database\Interface\CustomObject;

class GroupNavigation extends CustomObject
{
    protected $objectAttributes = [
        "securityGroupId" => self::TYPE_INTEGER,
        "navigationId" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "securityGroup" => [
            "securityGroupId" => \Database\Repository\Security\Group::class
        ],
        "navigation" => [
            "navigationId" => \Database\Repository\Navigation\Navigation::class
        ]
    ];
}
