<?php

namespace Controllers\APP;

use Security\User;
use Router\Helpers;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Database\Repository\Module;
use Controllers\DefaultController;
use Database\Repository\ModuleSetting;
use Database\Repository\UserHomeWorkDistance;

class SupervisionController extends DefaultController
{
	public function fill()
	{
		$this->write();
		$this->writeCalendarRanges();
		$this->cleanUp();
		return $this->getLayout();
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

	private function cleanUp()
	{
	}
}
