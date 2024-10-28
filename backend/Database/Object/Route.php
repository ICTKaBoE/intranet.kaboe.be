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
        "apiNoAuth" => "boolean",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "routeGroup" => ['routeGroupId' => \Database\Repository\RouteGroup::class]
    ];

    public function init()
    {
        $this->formatted->full = $this->linked->routeGroup->prefix . "/" . $this->route . "/";
    }
}
