<?php

namespace Database\Object\Setting;

use Ouzo\Utilities\Strings;
use Database\Interface\CustomObject;

class Setting extends CustomObject
{
    const SETTING_TEMPLATES = [
        'input' => '<input type="text" id="{{setting:id}}" name="{{setting:id}}" class="form-control" {{setting:formatted.readonly}} />',
        'password' => '<input type="password" id="{{setting:id}}" name="{{setting:id}}" class="form-control" {{setting:formatted.readonly}} />',
        'text' => '<textarea id="{{setting:id}}" name="{{setting:id}}" class="form-control" {{setting:formatted.readonly}}></textarea>',
        'switch' => '<div class="col-12 mb-3" id="chb{{setting.id}}" role="checkbox" data-type="checkbox" data-name="{{setting:id}}" data-text="{{setting:name}}"></div>',
        'select' => '<select id="{{setting:id}}" name="{{setting:id}}" class="form-select" {{setting:formatted.readonly}}>{{select:options}}</select>',
        'multipleselect' => '<select id="{{setting:id}}" name="{{setting:id}}" class="form-select" multiple {{setting:formatted.readonly}}>{{select:options}}</select>',
    ];

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
            "settingTabId" => \Database\Repository\Setting\Tab::class
        ]
    ];

    public function init()
    {
        $this->formatted->readonly = $this->readonly ? "readonly" : "";
        $this->formatted->settingHtml = self::SETTING_TEMPLATES[$this->type];

        $options = "";
        foreach ($this->options as $option) {
            [$key, $value] = Strings::contains($option, ":") ? explode(":", $option) : [$option, $option];
            $options .= "<option value=\"{$key}\">{$value}</option>";
        }

        foreach ($this->toArray(true) as $k => $v) $this->formatted->settingHtml = str_replace("{{setting:{$k}}}", $v, $this->formatted->settingHtml);
        $this->formatted->settingHtml = str_replace("{{select:options}}", $options, $this->formatted->settingHtml);

        $this->formatted->html = "  <div class='mb-3'>
						                <label class='form-label' for='{$this->id}'>{$this->name}</label>
                                        {$this->formatted->settingHtml}
						                <div class='invalid-feedback' data-feedback-input='{{setting:id}}'></div>
					                </div>";
    }
}
