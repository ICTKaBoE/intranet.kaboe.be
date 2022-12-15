<?php

namespace Controllers\API;

use Security\User;
use Ouzo\Utilities\Arrays;
use Controllers\ApiController;
use Database\Repository\Holliday;
use Database\Repository\BikeEventHomeWork;
use Ouzo\Utilities\Clock;
use Router\Helpers;

class CalendarController extends ApiController
{
	public function holliday()
	{
		$start = Clock::at(Helpers::url()->getParam('start'));
		$end = Clock::at(Helpers::url()->getParam('end'));

		$events = (new Holliday)->get();
		// $events = Arrays::filter($events, fn ($e) => Clock::at($e->start)->isAfterOrEqualTo($start) && Clock::at($e->end)->isBeforeOrEqualTo($end));

		foreach ($events as $event) {
			$event->link();
			$this->appendToJson(data: [
				"id" => $event->id,
				"start" => $event->start,
				"end" => $event->end,
				"title" => ($event->school ? $event->school->name . ": " : "") . $event->name,
				"allDay" => $event->fullDay
			]);
		}


		$this->handle();
	}

	public function homeWork()
	{
		$start = Clock::at(Helpers::url()->getParam('start'));
		$end = Clock::at(Helpers::url()->getParam('end'));

		$user = User::getLoggedInUser();
		$events = (new BikeEventHomeWork)->getByUserId($user->id);
		$events = Arrays::filter($events, fn ($e) => $e->distance > 0);
		// $events = Arrays::filter($events, fn ($e) => Clock::at($e->start)->isAfterOrEqualTo($start) && Clock::at($e->end)->isBeforeOrEqualTo($end));

		foreach ($events as $event) {
			$event->link();
			$this->appendToJson(data: [
				"start" => $event->date,
				"title" => round($event->distance, 2) . " km",
				"display" => "background",
				"classNames" => [
					"bg-{$event->userHomeWorkDistance->color}",
					"text-{$event->userHomeWorkDistance->textColor}"
				],
				"allDay" => true,
			]);
		}

		$this->handle();
	}
}
