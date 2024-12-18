<?php

namespace Controllers\PAGE\INTRANET;

use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Controllers\DefaultController;
use Database\Repository\SettingTab;
use Ouzo\Utilities\Arrays;

class ConfigurationGeneralController
{
	protected $layout = [
		"navItems" => [
			"pattern" => "{{configuration:general:navitems}}",
			"content" => "{{configuration:general:navitems}}"
		],
		"tabs" => [
			"pattern" => "{{configuration:general:tabs}}",
			"content" => "{{configuration:general:tabs}}"
		]
	];

	const SETTING_NAVITEMS_TEMPLATE = '<li class="nav-item"><a href="#tab-{{tab:id}}" class="nav-link {{tab:active}}" data-bs-toggle="tab">{{tab:iconData}}{{tab:name}}</a></li>{{configuration:general:navitems}}';
	const SETTING_TAB_TEMPLATE = '<div class="tab-pane {{tab:active}}" id="tab-{{tab:id}}">{{tab:settings}}</div>{{configuration:general:tabs}}';
	const SETTING_ROW_TEMPLATE = '<div class="mb-3">
						<label class="form-label" for="{{setting:id}}">{{setting:name}}</label>
						{{setting:input}}
						<div class="invalid-feedback" data-feedback-input="{{setting:id}}"></div>
					</div>';
	const SETTING_TEMPLATES = [
		'input' => '<input type="text" id="{{setting:id}}" name="{{setting:id}}" class="form-control" {{setting:formatted.readonly}} />',
		'password' => '<input type="password" id="{{setting:id}}" name="{{setting:id}}" class="form-control" {{setting:formatted.readonly}} />',
		'text' => '<textarea id="{{setting:id}}" name="{{setting:id}}" class="form-control" {{setting:formatted.readonly}}></textarea>',
		'switch' => '<label class="form-check form-switch"><input class="form-check-input" id="{{setting:id}}" name="{{setting:id}}" type="checkbox" {{setting:formatted.readonly}}><span class="form-check-label"></span></label>',
		'select' => '<select id="{{setting:id}}" name="{{setting:id}}" class="form-select" {{setting:formatted.readonly}}>{{select:options}}</select>',
		'multipleselect' => '<select id="{{setting:id}}" name="{{setting:id}}" class="form-select" multiple {{setting:formatted.readonly}}>{{select:options}}</select>',
	];

	public function write()
	{
		$this->createNavItems();
		$this->createTabItems();
		// $this->cleanUp();
		return $this->layout;
	}

	private function createNavItems()
	{
		$tabs = (new SettingTab)->get();

		foreach ($tabs as $index => $tab) {
			$template = self::SETTING_NAVITEMS_TEMPLATE;
			$template = str_replace("{{tab:active}}", ($index == 0 ? 'active' : ''), $template);

			foreach ($tab->toArray(true) as $key => $value) $template = str_replace("{{tab:{$key}}}", $value, $template);

			$this->layout["navItems"]["content"] = str_replace("{{configuration:general:navitems}}", $template, $this->layout["navItems"]["content"]);
		}
	}

	private function createTabItems()
	{
		$tabs = (new SettingTab)->get();

		foreach ($tabs as $index => $tab) {
			$template = self::SETTING_TAB_TEMPLATE;
			$template = str_replace("{{tab:active}}", ($index == 0 ? 'active show' : ''), $template);

			foreach ($tab->toArray(true) as $key => $value) $template = str_replace("{{tab:{$key}}}", $value, $template);

			$template = str_replace("{{tab:settings}}", $this->createTabSettings($tab->id), $template);
			$this->layout["tabs"]["content"] = str_replace("{{configuration:general:tabs}}", $template, $this->layout["tabs"]["content"]);
		}
	}

	private function createTabSettings($tabId)
	{
		$settings = (new Setting)->getBySettingTabId($tabId);
		$settings = Arrays::filter($settings, fn($s) => $s->order > 0);
		$settingsHtml = "";

		foreach ($settings as $setting) {
			$row = self::SETTING_ROW_TEMPLATE;
			$input = self::SETTING_TEMPLATES[$setting->type];


			$row = str_replace("{{setting:input}}", $input, $row);

			foreach ($setting->toArray(true) as $key => $value) {
				$row = str_replace("{{setting:{$key}}}", $value, $row);
			}

			if (Strings::equal($setting->type, 'select') || Strings::equal($setting->type, 'multipleselect')) {
				$optionHtml = "";
				foreach ($setting->options as $option) {
					[$key, $value] = Strings::contains($option, ":") ? explode(":", $option) : [$option, $option];
					$optionHtml .= "<option value=\"{$key}\">{$value}</option>";
				}

				$row = str_replace("{{select:options}}", $optionHtml, $row);
			}

			$settingsHtml .= $row;
		}

		return $settingsHtml;
	}
}
