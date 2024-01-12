<?php

namespace Controllers\PAGE\APP;

use Router\Helpers;
use Ouzo\Utilities\Clock;
use Database\Repository\Module;
use Controllers\DefaultController;
use Database\Repository\ModuleSetting;
use Database\Repository\School;

class SupervisionFillController extends DefaultController
{
	const TEMPLATE_LEGENDA = 	'<div class="row mb-1">
									<div class="col-2" style="background-color: {{school:color}}"></div>
									<div class="col">{{school:name}}</div>
								</div>{{fill:legenda}}';

	public function index()
	{
		$this->write();
		$this->writeLegenda();
		$this->writeCalendarRanges();
		return $this->getLayout();
	}

	private function writeLegenda()
	{
		$schools = (new School)->get();

		foreach ($schools as $school) {
			$template = self::TEMPLATE_LEGENDA;

			foreach ($school as $key => $value) $template = str_replace("{{school:{$key}}}", $value, $template);

			$this->layout = str_replace("{{fill:legenda}}", $template, $this->layout);
		}
	}

	private function writeCalendarRanges()
	{
		$moduleSettingRepo = new ModuleSetting;
		$moduleId = (new Module)->getByModule(Helpers::getModule())->id;

		$lastPayDate = $moduleSettingRepo->getByModuleAndKey($moduleId, "lastPayDate")->value;
		$blockFuture = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockFuture")->value == "on";
		$blockFutureAmount = (int)$moduleSettingRepo->getByModuleAndKey($moduleId, "blockFutureAmount")->value;
		$blockFutureType = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockFutureType")->value;
		$blockPast = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockPast")->value == "on";
		$blockPastAmount = (int)$moduleSettingRepo->getByModuleAndKey($moduleId, "blockPastAmount")->value;
		$blockPastType = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockPastType")->value;
		$blockPastOnLastPayDate = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockPastOnLastPayDate")->value == "on";

		$blockFutureDate = Clock::now();
		if ($blockFutureType == "d") $blockFutureDate = $blockFutureDate->plusDays($blockFutureAmount);
		else if ($blockFutureType == "w") $blockFutureDate = $blockFutureDate->plusDays($blockFutureAmount * 7);
		else if ($blockFutureType == "m") $blockFutureDate = $blockFutureDate->plusMonths($blockFutureAmount);
		else if ($blockFutureType == "y") $blockFutureDate = $blockFutureDate->plusYears($blockFutureAmount);

		$blockPastDate = Clock::now();
		if ($blockPastType == "d") $blockPastDate = $blockPastDate->minusDays($blockPastAmount);
		else if ($blockPastType == "w") $blockPastDate = $blockPastDate->minusDays($blockPastAmount * 7);
		else if ($blockPastType == "m") $blockPastDate = $blockPastDate->minusMonths($blockPastAmount);
		else if ($blockPastType == "y") $blockPastDate = $blockPastDate->minusYears($blockPastAmount);

		if ($blockPastOnLastPayDate) {
			if (Clock::at($lastPayDate)->isAfterOrEqualTo($blockPastDate)) $blockPastDate = Clock::at($lastPayDate)->plusDays(1);
		}

		$this->layout = str_replace("{{fill:range:start}}", ($blockPast ? $blockPastDate->format("Y-m-d") : ""), $this->layout);
		$this->layout = str_replace("{{fill:range:end}}", ($blockFuture ? $blockFutureDate->format("Y-m-d") : ""), $this->layout);
	}
}
