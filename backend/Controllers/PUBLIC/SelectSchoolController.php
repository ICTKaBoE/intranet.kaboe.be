<?php

namespace Controllers\PUBLIC;

use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Module;
use Database\Repository\Setting;
use Controllers\DefaultController;
use Database\Repository\School;
use Database\Repository\SettingOverride;

class SelectSchoolController extends DefaultController
{
	const TEMPLATE_SCHOOL_BUTTON = '<div class="col-sm-6 col-lg-3">
            <a class="card card-sm" href="{{school:link}}" target="{{school:target}}" style="background-color: {{school:color}}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="font-weight-medium markdown">
                                <h1>{{school:name}}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>{{school:buttons}}';

	public function index()
	{
		$this->write();
		$this->writeButtons();
		$this->cleanUp();
		return $this->getLayout();
	}

	private function writeButtons()
	{
		$schools = (new School)->get();

		foreach ($schools as $school) {
			$buttonTemplate = self::TEMPLATE_SCHOOL_BUTTON;

			$buttonTemplate = str_replace('{{school:link}}', "/public/notescreen/viewscreen/{$school->id}", $buttonTemplate);
			$buttonTemplate = str_replace('{{school:target}}', "_self", $buttonTemplate);
			$buttonTemplate = str_replace('{{school:color}}', $school->color, $buttonTemplate);
			$buttonTemplate = str_replace('{{school:name}}', $school->name, $buttonTemplate);

			$this->layout = str_replace("{{school:buttons}}", $buttonTemplate, $this->layout);
		}
	}

	private function cleanUp()
	{
		$this->layout = str_replace("{{school:buttons}}", "", $this->layout);
	}
}
