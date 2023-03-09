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
}
