<?php

namespace Controllers\API;

use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Database\Repository\Helpdesk;
use Database\Repository\HelpdeskAction;
use Database\Repository\HelpdeskThread;
use Database\Repository\BikeEventHomeWork;

class CleanController extends ApiController
{
	public function start()
	{
		$this->cleanBikeEvents();
		$this->cleanHelpdesk();
		$this->cleanDownloads();
		$this->handle();
	}

	private function cleanBikeEvents()
	{
		$cleanDate = CLEAN_DATES["bikeEvent"];
		$now = Clock::now();
		$deleteBefore = $this->calculateDeleteBefore($now, $cleanDate);

		if ($cleanDate["date"] && !Strings::equal($now->format("n-d"), $cleanDate["date"])) return;

		$bikeEventRepo = new BikeEventHomeWork;
		$events = Arrays::filter($bikeEventRepo->get(), fn ($b) => Clock::at($b->date)->isBeforeOrEqualTo($deleteBefore));

		foreach ($events as $event) {
			$event->deleted = true;
			$bikeEventRepo->set($event);
		}

		$bikeEventRepo->deleteWhereDeleteTrue();
	}

	private function cleanHelpdesk()
	{
		$cleanDate = CLEAN_DATES["helpdesk"];
		$now = Clock::now();
		$deleteBefore = $this->calculateDeleteBefore($now, $cleanDate);

		if ($cleanDate["date"] && !Strings::equal($now->format("n-d"), $cleanDate["date"])) return;

		$helpdeskRepo = new Helpdesk;
		$helpdeskActionRepo = new HelpdeskAction;
		$helpdeskThreadRepo = new HelpdeskThread;

		$helpdesks = Arrays::filter($helpdeskRepo->get(), fn ($h) => Clock::at($h->lastActionDateTime)->isBeforeOrEqualTo($deleteBefore));

		foreach ($helpdesks as $helpdesk) {
			$helpdesk->deleted = true;
			$helpdeskRepo->set($helpdesk);

			foreach ($helpdeskActionRepo->getByHelpdeskId($helpdesk->id) as $ha) {
				$ha->deleted = true;
				$helpdeskActionRepo->set($ha);
			}

			foreach ($helpdeskThreadRepo->getByHelpdeskId($helpdesk->id) as $ht) {
				$ht->deleted = true;
				$helpdeskThreadRepo->set($ht);
			}
		}

		$helpdeskRepo->deleteWhereDeleteTrue();
		$helpdeskActionRepo->deleteWhereDeleteTrue();
		$helpdeskThreadRepo->deleteWhereDeleteTrue();
	}

	private function cleanDownloads()
	{
		if (!file_exists(LOCATION_DOWNLOAD)) return;

		$dir = LOCATION_DOWNLOAD;
		$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator(
			$it,
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($files as $file) {
			if ($file->isDir()) {
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}

		rmdir($dir);
	}

	private function calculateDeleteBefore($now, $cleanDate)
	{
		return $now
			->minusYears($cleanDate["year"] ?? 0)
			->minusMonths($cleanDate["month"] ?? 0)
			->minusDays($cleanDate["day"] ?? 0)
			->minusHours($cleanDate["hour"] ?? 0)
			->minusMinutes($cleanDate["minute"] ?? 0)
			->minusSeconds($cleanDate["seconds"] ?? 0);
	}
}
