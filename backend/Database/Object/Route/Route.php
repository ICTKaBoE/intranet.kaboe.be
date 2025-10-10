<?php

namespace Database\Object\Route;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Path;

class Route extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "routeGroupId" => self::TYPE_INTEGER,
        "method" => self::TYPE_STRING,
        "route" => self::TYPE_STRING,
        "controller" => self::TYPE_STRING,
        "callback" => self::TYPE_STRING,
        "apiNoAuth" => self::TYPE_BOOLEAN,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        // "routeGroup" => ['routeGroupId' => \Database\Repository\Route\Group::class]
    ];

    public function init()
    {
        $this->formatted->full = Path::normalize($this->linked->routeGroup->prefix . "/" . $this->route) . "/";
    }
}
