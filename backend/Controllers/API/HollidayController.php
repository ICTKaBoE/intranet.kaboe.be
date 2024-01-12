<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\Holliday;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

class HollidayController extends ApiController
{
	public function getHolliday($view)
	{
		$hollidays = (new Holliday)->get();

		if ($view == "calendar") {
			foreach ($hollidays as $event) {
				$event->link();
				if ($event->fullDay && !Strings::equal($event->start, $event->end)) $event->end = Clock::at($event->end)->plusDays(1)->format("Y-m-d");

				$this->appendToJson(data: [
					"id" => $event->id,
					"start" => $event->start,
					"end" => $event->end,
					"title" => ($event->school ? $event->school->name . ": " : "") . $event->name,
					"allDay" => $event->fullDay
				]);
			}
		}

		$this->handle();
	}
}
