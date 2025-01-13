<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\HTML;

class SettingTab extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "name" => "string",
        "icon" => "string",
        "order" => "int",
        "default" => "boolean",
        "deleted" => "boolean"
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
