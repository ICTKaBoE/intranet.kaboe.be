<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Route extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "routeGroupId" => "int",
        "method" => "string",
        "route" => "string",
        "controller" => "string",
        "callback" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "routeGroup" => ['routeGroupId' => \Database\Repository\RouteGroup::class]
    ];
}
