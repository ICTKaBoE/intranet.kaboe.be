<?php

namespace Controllers;

use Database\Repository\Module;
use Database\Repository\Setting;
use Database\Repository\SettingOverride;

class SelectModuleController extends Controller
{
	const TEMPLATE_MODULE_BUTTON = '<div class="col-sm-6 col-lg-3">
            <a class="card card-sm" href="{{module:link}}">
                <div class="card-body">
                    <div class="row align-items-center">
						{{module:icon}}
                        <div class="col">
                            <div class="font-weight-medium markdown">
                                <h1>{{module:name}}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>';

	const TEMPLATE_MODULE_ICON = '<div class="col-auto">
                                <span class="bg-{{module:iconBackgroundColor}} text-white avatar avatar-lg">
                                    {{module:iconData}}
                                </span>
                            </div>';

	function index()
	{
		$this->write();
		$buttons = $this->createButtons();

		$this->layout = str_replace("{{module:buttons}}", $buttons, $this->layout);

		return $this->layout;
	}

	private function createButtons()
	{
		$html = "";
		$modules = (new Module)->get();

		foreach ($modules as $module) {
			$buttonTemplate = self::TEMPLATE_MODULE_BUTTON;
			$iconTemplate = self::TEMPLATE_MODULE_ICON;

			$toolDefaultPage = (new Setting)->get("page.default.tool")[0]->value;
			$toolDefaultPageOverride = (new SettingOverride)->getBySettingAndModule("page.default.tool", $module->id)->value;

			if (!is_null($toolDefaultPageOverride)) {
				$toolDefaultPage = $toolDefaultPageOverride;
			}

			$buttonTemplate = str_replace('{{module:link}}', $module->link . $toolDefaultPage, $buttonTemplate);
			$buttonTemplate = str_replace('{{module:name}}', $module->name, $buttonTemplate);

			if ($module->icon) {
				$iconTemplate = str_replace('{{module:iconBackgroundColor}}', $module->iconBackgroundColor, $iconTemplate);
				$iconTemplate = str_replace('{{module:iconData}}', $module->iconData, $iconTemplate);

				$buttonTemplate = str_replace('{{module:icon}}', $iconTemplate, $buttonTemplate);
			} else $buttonTemplate = str_replace('{{module:icon}}', '', $buttonTemplate);

			$html .= $buttonTemplate;
		}

		return $html;
	}
}
