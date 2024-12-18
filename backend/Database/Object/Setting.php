<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class Setting extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "settingTabId" => "int",
        "name" => "string",
        "type" => "string",
        "options" => "list",
        "value" => "string",
        "readonly" => "boolean",
        "order" => "int",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "settingTab" => [
            "settingTabId" => \Database\Repository\SettingTab::class
        ]
    ];

    public function init()
    {
        $this->formatted->readonly = $this->readonly ? "readonly" : "";
    }
}
