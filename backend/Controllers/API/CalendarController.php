<?php

namespace Controllers\API;

use Security\User;
use Ouzo\Utilities\Arrays;
use Controllers\ApiController;
use Database\Repository\Holliday;
use Database\Repository\BikeEventHomeWork;
use Database\Repository\SupervisionEvent;

class CalendarController extends ApiController
{
	public function holliday()
	{
		$events = (new Holliday)->get();

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
		$user = User::getLoggedInUser();
		$events = (new BikeEventHomeWork)->getByUserId($user->id);
		$events = Arrays::filter($events, fn ($e) => $e->distance > 0);

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

	public function supervision()
	{
		$user = User::getLoggedInUser();
		$events = (new SupervisionEvent)->getByUserId($user->id);
		$events = Arrays::filter($events, fn ($e) => !(is_null($e->start) && is_null($e->end)));

		foreach ($events as $event) {
			$event->link();
			$this->appendToJson(data: [
				"id" => $event->id,
				"start" => $event->start,
				"end" => $event->end,
				"classNames" => [
					"bg-green",
					"text-white"
				],
			]);
		}

		$this->handle();
	}
}
