<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Strings;
use Router\Helpers;

class Navigation extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "parentId" => "int",
        "order" => "int",
        "redirect" => "boolean",
        "link" => "string",
        "name" => "string",
        "icon" => "string",
        "minimumRights" => "binary",
        "settings" => "json",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "parent" => [
            "parentId" => \Database\Repository\Navigation::class
        ]
    ];

    public function init()
    {
        $this->formatted->link = $this->redirect ? $this->link : Path::normalize("/" . ($this->linked->parent ? $this->linked->parent->formatted->link . "/" : "") . $this->link);
        $this->formatted->active = Strings::contains(Helpers::getReletiveUrl(), $this->formatted->link);
        $this->formatted->target = $this->redirect ? "_blank" : "_self";

        $this->formatted->isActive = $this->formatted->active ? 'active' : '';
        $this->formatted->isShow = $this->formatted->active ? 'show' : '';
        $this->formatted->isAriaExpanded = $this->formatted->active ? 'true' : 'false';
    }
}
