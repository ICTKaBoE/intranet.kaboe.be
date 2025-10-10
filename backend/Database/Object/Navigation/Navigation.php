<?php

namespace Database\Object\Navigation;

use Database\Interface\CustomObject;
use Database\Repository\Navigation\Navigation as NavigationNavigation;
use Database\Repository\Navigation\Setting;
use Helpers\HTML;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Strings;
use Router\Helpers;

class Navigation extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "routeGroupId" => self::TYPE_STRING,
        "parentId" => self::TYPE_INTEGER,
        "type" => self::TYPE_STRING,
        "order" => self::TYPE_INTEGER,
        "default" => self::TYPE_BOOLEAN,
        "link" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "icon" => self::TYPE_STRING,
        "color" => self::TYPE_STRING,
        "settings" => self::TYPE_JSON,
        "deleted" => self::TYPE_BOOLEAN,
    ];

    protected $linkedAttributes = [
        "parent" => [
            "parentId" => \Database\Repository\Navigation\Navigation::class
        ]
    ];

    public function init()
    {
        $default = $this->type == "M" ? (new Setting)->getByNavigationIdAndKey($this->id, "_")->value : null;

        $this->formatted->link =
            $this->type == "L" ?
            $this->link : (
                $this->type == "F" ?
                "#{$this->id}" :
                Path::normalize("/" . (
                    $this->linked->parent && $this->linked->parent->type !== "F" ?
                    $this->linked->parent->formatted->link . "/" :
                    ""
                ) . $this->link)
            );
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
