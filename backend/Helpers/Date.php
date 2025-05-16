<?php

namespace Helpers;

use Ouzo\Utilities\Clock;

abstract class Date
{
	static public function monthsBetweenDates($start, $end, $format = "m/Y")
	{
		$months = [];

		$start = Clock::at($start);
		$end = Clock::at($end);

		do {
			$months[] = $start->format($format);
			$start = $start->plusMonths(1);
		} while ($start->isBeforeOrEqualTo($end));

		return $months;
	}

	static public function workingDays($year, $month)
	{
		$first = strtotime("{$year}-{$month}-01");
		$last = strtotime("last day of {$year}-{$month}");

		$workingDays = 0;

		for ($day = $first; $day <= $last; $day = strtotime("+1 day", $day)) {
			if (date("N", $day) <= 5) $workingDays++;
		}

		return $workingDays;
	}
}
