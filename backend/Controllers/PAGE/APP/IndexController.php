<?php

namespace Controllers\PAGE\APP;

use Security\User;
use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Module;
use Database\Repository\Setting;
use Controllers\DefaultController;
use Database\Repository\SettingOverride;

class IndexController extends DefaultController
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

	const TEMPLATE_MODULE_ICON = 	'<div class="col-auto">
										<span class="bg-{{module:iconBackgroundColor}} text-white avatar avatar-lg">
											<i class="icon ti ti-{{module:icon}}"></i>
										</span>
									</div>';

	public function index()
	{
		$this->write();
		$this->writeButtons();
		return $this->getLayout();
	}

	private function writeButtons()
	{
		$modules = (new Module)->getByScope((Helpers::isPublicPage() ? "public" : "app"));
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
				$iconTemplate = str_replace('{{module:icon}}', $module->icon, $iconTemplate);

				$buttonTemplate = str_replace('{{module:icon}}', $iconTemplate, $buttonTemplate);
			} else $buttonTemplate = str_replace('{{module:icon}}', '', $buttonTemplate);

			$this->layout = str_replace("{{module:buttons}}", $buttonTemplate, $this->layout);
		}
	}
}
