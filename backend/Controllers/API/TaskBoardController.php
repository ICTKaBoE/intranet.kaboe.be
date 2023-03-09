<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Repository\Maintenance;
use Database\Repository\School;
use Helpers\Icon;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

class TaskBoardController extends ApiController
{
	public function maintenanceRequests($prefix, $schoolId = null)
	{
		$preloadIcons = [
			"calendar" => Icon::load("calendar"),
			"device-watch" => Icon::load("device-watch"),
			"check" => Icon::load("check"),
			"map-pin" => Icon::load("map-pin"),
			"user" => Icon::load("user")
		];

		$schools = (new School)->get($schoolId);
		$columns = [
			[
				"id" => "todo",
				"name" => Icon::load("list") . " To Do (oud -> nieuw)"
			],
			[
				"id" => "inprogress",
				"name" => Icon::load("rotate") . " In Uitvoering (nieuw -> oud)",
				"order" => "reverse"
			],
			[
				"id" => "waiting",
				"name" => Icon::load("hourglass-empty") . " In Afwachting (nieuw -> oud)",
				"order" => "reverse"
			],
			[
				"id" => "completed",
				"name" => Icon::load("check") . " Afgewerkt (laatste 7 dagen (nieuw -> oud))",
				"order" => "reverse"
			]
		];
		$maintenance = new Maintenance;
		$jobsPerSchoolPerStatus = [];

		foreach ($schools as $school) {
			$jobs = $maintenance->getBySchoolId($school->id);

			foreach ($columns as $column) {
				$returnJobs = [];
				$actions = [];

				if (Strings::equalsIgnoreCase($column['id'], "todo")) $actions = ["setInProgress", "setWaiting", "setCompleted"];
				else if (Strings::equalsIgnoreCase($column['id'], "inprogress")) $actions = ["setWaiting", "setCompleted"];
				else if (Strings::equalsIgnoreCase($column['id'], "waiting")) $actions = ["setInProgress", "setCompleted"];

				$jobsFiltered = Arrays::filter($jobs, fn ($j) => Strings::equal($j->status, $column['id']));
				$jobsFiltered = Arrays::filter($jobsFiltered, fn ($j) => is_null($j->lastActionDateTime) || Clock::at($j->lastActionDateTime)->isAfterOrEqualTo(Clock::now()->minusDays(7)));
				$jobsFiltered = Arrays::orderBy($jobsFiltered, "date");

				if (isset($column['order']) && $column['order'] === "reverse") $jobsFiltered = array_reverse($jobsFiltered);

				foreach ($jobsFiltered as $j) {
					$returnJobs[] = [
						"id" => $j->id,
						"postDate" => is_null($j->creationDate) ? false : $preloadIcons["calendar"] . " " . Clock::at($j->creationDate)->format("d/m/Y H:i:s"),
						"finishedByDate" => is_null($j->finishedByDate) ? false : $preloadIcons["device-watch"] . " " . Clock::at($j->finishedByDate)->format("d/m/Y"),
						"finishedAt" => ($j->status !== "completed" || is_null($j->lastActionDateTime)) ? false : $preloadIcons["check"] . " " . Clock::at($j->lastActionDateTime)->format("d/m/Y"),
						"location" => is_null($j->location) ? false : $preloadIcons["map-pin"] . " " . $j->location,
						"executeBy" => is_null($j->executeBy) ? false : $preloadIcons["user"] . " " . $j->executeBy,
						"title" => is_null($j->subject) ? false : $j->subject,
						"body" => is_null($j->details) ? false : trim($j->details),
						"priority" => $j->status === "completed" ? false : $j->priority,
						"actions" => $actions
					];
				}

				$jobsPerSchoolPerStatus[$school->id][$column['id']] = $returnJobs;
			}
		}

		$this->appendToJson("tabs", $schools);
		$this->appendToJson("columns", $columns);
		$this->appendToJson("jobs", $jobsPerSchoolPerStatus);
		$this->handle();
	}
}
