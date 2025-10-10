<?php

namespace Database\Object\Setting;

use Database\Interface\CustomObject;
use Helpers\HTML;

class Tab extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "icon" => self::TYPE_STRING,
        "order" => self::TYPE_INTEGER,
        "default" => self::TYPE_BOOLEAN,
        "deleted" => self::TYPE_BOOLEAN
    ];

    public function init()
    {
        $this->active = $this->default ? "active" : "";
        $this->contentActive = $this->default ? "active show" : "";

        $this->formatted->icon->icon = HTML::Icon($this->icon);
        $this->formatted->html = "<li class='nav-item'><a href='#tab-{$this->id}' class='nav-link {$this->active}' data-bs-toggle='tab'>{$this->formatted->icon->icon} {$this->name}</a></li>";
        $this->formatted->contentHtml = "<div class='tab-pane {$this->contentActive}' id='tab-{$this->id}'>{{settings}}</div>";
    }

    public function settings($settings)
    {
        $this->formatted->contentHtml = str_replace("{{settings}}", $settings, $this->formatted->contentHtml);
    }
}
