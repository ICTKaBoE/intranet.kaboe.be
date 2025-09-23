<?php

namespace Database\Object\Navigation;

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
        "folderId" => "int",
        "type" => "string",
        "order" => "int",
        "link" => "string",
        "name" => "string",
        "icon" => "string",
        "color" => "string",
        "settings" => "json",
        "deleted" => "boolean",
    ];

    protected $linkedAttributes = [
        "parent" => [
            "parentId" => \Database\Repository\Navigation\Navigation::class
        ]
    ];

    public function init()
    {
        $default = $this->settings["_"] ?: null;

        $this->formatted->link = $this->type == "L" ? $this->link : ($this->type == "F" ? "#{$this->link}" : Path::normalize("/" . ($this->linked->parent && $this->linked->parent->type !== "F" ? $this->linked->parent->formatted->link . "/" : "") . $this->link));
        $this->formatted->linkWithDefault = $this->formatted->link . ($default ? "/{$default}" : "");

        $this->formatted->active = Strings::contains(Helpers::getReletiveUrl(), $this->formatted->link);
        $this->formatted->target = $this->type == "L" ? "_blank" : "_self";

        $this->name = $this->type == "L" ? $this->name . HTML::Icon("external-link", class: ['ms-2']) : $this->name;
        $this->name = $this->type == "F" ? $this->name . HTML::Icon("folder", class: ['ms-2']) : $this->name;

        $this->formatted->icon->dashboard = HTML::Icon($this->icon, style: ["font-size" => "4rem"]);

        $this->formatted->isActive = $this->formatted->active ? 'active' : '';
        $this->formatted->isShow = $this->formatted->active ? 'show' : '';
        $this->formatted->isAriaExpanded = $this->formatted->active ? 'true' : 'false';
    }
}
