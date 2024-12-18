<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\HTML;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Strings;
use Router\Helpers;

class Navigation extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "routeGroupId" => "string",
        "parentId" => "int",
        "order" => "int",
        "redirect" => "boolean",
        "link" => "string",
        "name" => "string",
        "icon" => "string",
        "color" => "string",
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
        $default = $this->settings["_"] ?: null;
        $this->formatted->link = $this->redirect ? $this->link : Path::normalize("/" . ($this->linked->parent ? $this->linked->parent->formatted->link . "/" : "") . $this->link);
        $this->formatted->linkWithDefault = $this->formatted->link . ($default ? "/{$default}" : "");
        $this->formatted->active = Strings::contains(Helpers::getReletiveUrl(), $this->formatted->link);
        $this->formatted->target = $this->redirect ? "_blank" : "_self";

        $this->formatted->icon->dashboard = HTML::Icon($this->icon, style: ["font-size" => "4rem"]);

        $this->formatted->isActive = $this->formatted->active ? 'active' : '';
        $this->formatted->isShow = $this->formatted->active ? 'show' : '';
        $this->formatted->isAriaExpanded = $this->formatted->active ? 'true' : 'false';
    }
}
