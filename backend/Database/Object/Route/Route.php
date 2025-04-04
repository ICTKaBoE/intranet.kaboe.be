<?php

namespace Database\Object\Route;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Path;

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
        // "routeGroup" => ['routeGroupId' => \Database\Repository\Route\Group::class]
    ];

    public function init()
    {
        $this->formatted->full = Path::normalize($this->linked->routeGroup->prefix . "/" . $this->route) . "/";
    }
}
