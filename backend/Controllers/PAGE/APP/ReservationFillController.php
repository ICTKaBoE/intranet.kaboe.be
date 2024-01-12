<?php

namespace Controllers\PAGE\APP;

use Router\Helpers;
use Helpers\Mapping;
use Ouzo\Utilities\Clock;
use Database\Repository\Module;
use Database\Repository\School;
use Controllers\DefaultController;
use Database\Repository\ModuleSetting;

class ReservationFillController extends DefaultController
{
	const TEMPLATE_LEGENDA = 	'<div class="row mb-1">
									<div class="col-2 bg-{{typeName.color}}"></div>
									<div class="col">{{typeName.description}}</div>
								</div>{{fillcalendar:legenda}}';

    public function index()
    {
		$this->write();
		$this->writeLegenda();
        $this->writeCalendarRanges();
        return $this->getLayout();
    }

	private function writeLegenda()
	{
		$typeNames = Mapping::get("reservation/type");

		foreach ($typeNames as $typeName) {

			$template = self::TEMPLATE_LEGENDA;

			foreach ($typeName as $key => $value) $template = str_replace("{{typeName.{$key}}}", $value, $template);

			$this->layout = str_replace("{{fillcalendar:legenda}}", $template, $this->layout);
		}
	}


    private function writeCalendarRanges()
    {
        $moduleSettingRepo = new ModuleSetting;
        $moduleId = (new Module)->getByModule(Helpers::getModule())->id;

		$blockFuture = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockFuture")->value == "on";
		$blockFutureAmount = (int)$moduleSettingRepo->getByModuleAndKey($moduleId, "blockFutureAmount")->value;
		$blockFutureType = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockFutureType")->value;
		$blockPast = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockPast")->value == "on";
		$blockPastAmount = (int)$moduleSettingRepo->getByModuleAndKey($moduleId, "blockPastAmount")->value;
		$blockPastType = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockPastType")->value;

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

        $this->layout = str_replace("{{fillcalendar:range:start}}", ($blockPast ? $blockPastDate->format("Y-m-d") : ""), $this->layout);
		$this->layout = str_replace("{{fillcalendar:range:end}}", ($blockFuture ? $blockFutureDate->format("Y-m-d") : ""), $this->layout);
    }
}