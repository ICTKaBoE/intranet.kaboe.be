<?php

namespace Controllers\APP;

use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Module;
use Database\Repository\Setting;
use Controllers\DefaultController;
use Database\Repository\SettingOverride;
use Database\Repository\UserStart;
use Security\User;

class SelectModuleController extends DefaultController
{
	const TEMPLATE_MODULE_BUTTON = '<div class="col-sm-6 col-lg-3">
            <a class="card card-sm" href="{{module:link}}" target="{{module:target}}">
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
        </div>{{module:buttons}}';

	const TEMPLATE_MODULE_ICON = '<div class="col-auto">
                                <span class="bg-{{module:iconBackgroundColor}} text-white avatar avatar-lg">
                                    {{module:iconData}}
                                </span>
                            </div>';

	const TEMPLATE_USER_EMPTY = '<div class="col-12">
            <a class="card card-sm" href="/app/user/start">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="font-weight-medium markdown">
                                <h1>Geen eigen items? Deze kan je hier toevoegen</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>';

	const TEMPLATE_USER_I = '<div class="col-{{start:width}}">
            <a class="card card-sm" href="{{start:url}}" target="_blank">
                <div class="card-body">
                    <div class="row align-items-center">
						<div class="col-auto">
							<span class="text-white avatar avatar-lg">
								<img src="{{start:icon}}"/>
							</span>
						</div>
                    </div>
                </div>
            </a>
        </div>{{start:buttons}}';

	const TEMPLATE_USER_IN = '<div class="col-{{start:width}}">
            <a class="card card-sm" href="{{start:url}}" target="_blank">
                <div class="card-body">
                    <div class="row align-items-center">
						<div class="col-auto">
							<span class="text-white avatar avatar-lg">
								<img src="{{start:icon}}"/>
							</span>
						</div>
                        <div class="col">
                            <div class="font-weight-medium markdown">
                                <h1>{{start:name}}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>{{start:buttons}}';

	const TEMPLATE_USER_N = '<div class="col-{{start:width}}">
            <a class="card card-sm" href="{{start:url}}" target="_blank">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="font-weight-medium markdown">
                                <h1>{{start:name}}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>{{start:buttons}}';

	public function index()
	{
		$this->write();
		$this->writeButtons();
		$this->writeUserStart();
		$this->cleanUp();
		return $this->getLayout();
	}

	private function writeButtons()
	{
		$modules = (new Module)->getByScope(trim(Helpers::getPrefix(), "/"));
		$modules = Arrays::filter($modules, fn ($m) => User::hasPermissionToEnter($m->id, User::getLoggedInUser()->id));

		foreach ($modules as $module) {
			$buttonTemplate = self::TEMPLATE_MODULE_BUTTON;
			$iconTemplate = self::TEMPLATE_MODULE_ICON;

			$toolDefaultPage = (new Setting)->get("page.default.tool")[0]->value;
			$toolDefaultPageOverride = (new SettingOverride)->getBySettingAndModule("page.default.tool", $module->id)->value;

			if (!is_null($toolDefaultPageOverride)) {
				$toolDefaultPage = $toolDefaultPageOverride;
			}

			$buttonTemplate = str_replace('{{module:link}}', Strings::isBlank($module->redirect) ? ($module->link . $toolDefaultPage) : $module->link, $buttonTemplate);
			$buttonTemplate = str_replace('{{module:name}}', $module->name, $buttonTemplate);
			$buttonTemplate = str_replace('{{module:target}}', $module->target, $buttonTemplate);

			if ($module->icon) {
				$iconTemplate = str_replace('{{module:iconBackgroundColor}}', $module->iconBackgroundColor, $iconTemplate);
				$iconTemplate = str_replace('{{module:iconData}}', $module->iconData, $iconTemplate);

				$buttonTemplate = str_replace('{{module:icon}}', $iconTemplate, $buttonTemplate);
			} else $buttonTemplate = str_replace('{{module:icon}}', '', $buttonTemplate);

			$this->layout = str_replace("{{module:buttons}}", $buttonTemplate, $this->layout);
		}
	}

	private function writeUserStart()
	{
		$userStart = (new UserStart)->getByUserId(User::getLoggedInUser()->id);
		$template = self::TEMPLATE_USER_EMPTY;

		if (empty($userStart)) {
			$this->layout = str_replace("{{start:buttons}}", $template, $this->layout);
		} else {
			foreach ($userStart as $button) {
				if (Strings::equal($button->type, "I")) $template = self::TEMPLATE_USER_I;
				else if (Strings::equal($button->type, "IN")) $template = self::TEMPLATE_USER_IN;
				else if (Strings::equal($button->type, "N")) $template = self::TEMPLATE_USER_N;

				if (!is_null($template)) {
					foreach ($button as $key => $value) $template = str_replace("{{start:{$key}}}", $value, $template);

					$this->layout = str_replace("{{start:buttons}}", $template, $this->layout);
				}
			}
		}
	}

	private function cleanUp()
	{
		$this->layout = str_replace("{{module:buttons}}", "", $this->layout);
		$this->layout = str_replace("{{start:buttons}}", "", $this->layout);
	}
}
