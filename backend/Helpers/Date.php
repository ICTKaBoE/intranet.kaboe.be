<?php

namespace Helpers;

use Ouzo\Utilities\Arrays;
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

	static public function dayOfWeekToString($number, $language = "nl")
	{
		return Arrays::getNestedValue(WEEK_DAYS, [$language, $number]) ?: date('l', strtotime("Sunday +{$number} days"));
	}

	static public function stringToDayOfWeek($string, $language = "nl")
	{
		return Arrays::findKeyByValue(WEEK_DAYS[$language], $string);
	}
}
