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
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "parent" => [
            "parentId" => \Database\Repository\Navigation::class
        ]
    ];

    public function init()
    {
        $this->link = $this->redirect ? $this->link : Path::normalize("/" . ($this->parent ? $this->parent->link . "/" : "") . $this->link);
        $this->active = Strings::contains(Helpers::getReletiveUrl(), $this->link);
        $this->target = $this->redirect ? "_blank" : "_self";

        $this->isActive = $this->active ? 'active' : '';
        $this->isShow = $this->active ? 'show' : '';
        $this->isAriaExpanded = $this->active ? 'true' : 'false';
    }
}
