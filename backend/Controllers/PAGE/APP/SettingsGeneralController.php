<?php

namespace Controllers\PAGE\APP;

use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Controllers\DefaultController;
use Database\Repository\SettingTab;

class SettingsGeneralController extends DefaultController
{
	const SETTING_NAVITEMS_TEMPLATE = '<li class="nav-item"><a href="#tab-{{tab:id}}" class="nav-link {{tab:active}}" data-bs-toggle="tab">{{tab:iconData}}{{tab:name}}</a></li>{{settings:general:navitems}}';
	const SETTING_TAB_TEMPLATE = '<div class="tab-pane {{tab:active}}" id="tab-{{tab:id}}">{{tab:settings}}</div>{{settings:general:tabs}}';
	const SETTING_ROW_TEMPLATE = '<div class="mb-3">
						<label class="form-label" for="{{setting:id}}">{{setting:name}}</label>
						{{setting:input}}
						<div class="invalid-feedback" data-feedback-input="{{setting:id}}"></div>
					</div>';
	const SETTING_TEMPLATES = [
		'input' => '<input type="text" id="{{setting:id}}" name="{{setting:id}}" class="form-control" />',
		'password' => '<input type="password" id="{{setting:id}}" name="{{setting:id}}" class="form-control" />',
		'text' => '<textarea id="{{setting:id}}" name="{{setting:id}}" class="form-control"></textarea>',
		'switch' => '<label class="form-check form-switch"><input class="form-check-input" id="{{setting:id}}" name="{{setting:id}}" type="checkbox"><span class="form-check-label"></span></label>',
		'select' => '<select id="{{setting:id}}" name="{{setting:id}}" class="form-select">{{select:options}}</select>',
		'multipleselect' => '<select id="{{setting:id}}" name="{{setting:id}}" class="form-select" multiple>{{select:options}}</select>',
	];

	public function index()
	{
		$this->write();
		$this->createNavItems();
		$this->createTabItems();
		$this->cleanUp();
		return $this->getLayout();
	}

	private function createNavItems()
	{
		$tabs = (new SettingTab)->get();

		foreach ($tabs as $index => $tab) {
			$template = self::SETTING_NAVITEMS_TEMPLATE;
			$template = str_replace("{{tab:active}}", ($index == 0 ? 'active' : ''), $template);

			foreach ($tab as $key => $value) $template = str_replace("{{tab:{$key}}}", $value, $template);

			$this->layout = str_replace("{{settings:general:navitems}}", $template, $this->layout);
		}
	}

	private function createTabItems()
	{
		$tabs = (new SettingTab)->get();

		foreach ($tabs as $index => $tab) {
			$template = self::SETTING_TAB_TEMPLATE;
			$template = str_replace("{{tab:active}}", ($index == 0 ? 'active show' : ''), $template);

			foreach ($tab as $key => $value) $template = str_replace("{{tab:{$key}}}", $value, $template);

			$template = str_replace("{{tab:settings}}", $this->createTabSettings($tab->id), $template);
			$this->layout = str_replace("{{settings:general:tabs}}", $template, $this->layout);
		}
	}

	private function createTabSettings($tabId)
	{
		$settings = (new Setting)->getByTabId($tabId);
		$settingsHtml = "";

		foreach ($settings as $setting) {
			$row = self::SETTING_ROW_TEMPLATE;
			$input = self::SETTING_TEMPLATES[$setting->type];


			$row = str_replace("{{setting:input}}", $input, $row);

			foreach ($setting as $key => $value) {
				$row = str_replace("{{setting:{$key}}}", $value, $row);
			}

			if (Strings::equal($setting->type, 'select') || Strings::equal($setting->type, 'multipleselect')) {
				$optionHtml = "";
				foreach (explode(";", $setting->options) as $option) {
					[$key, $value] = explode(":", $option);
					$optionHtml .= "<option value=\"{$key}\">{$value}</option>";
				}

				$row = str_replace("{{select:options}}", $optionHtml, $row);
			}

			$settingsHtml .= $row;
		}

		return $settingsHtml;
	}

	private function cleanUp()
	{
		$this->layout = str_replace("{{settings:general:navitems}}", "", $this->layout);
		$this->layout = str_replace("{{settings:general:tabs}}", "", $this->layout);
	}
}
