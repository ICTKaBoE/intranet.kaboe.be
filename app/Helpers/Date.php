<?php

namespace Helpers;

use Core\Config;
use Ouzo\Utilities\Clock;

abstract class Date
{
    static public function monthsBetweenDates($start, $end, $formatLong = true)
    {
        $return = [];

        if (!$start instanceof Clock) $start = Clock::at($start);
        if (!$end instanceof Clock) $end = Clock::at($end);

        while ($start->isBeforeOrEqualTo($end)) {
            $current = $start;
            $monthIndex = (int)$current->format("n") - 1;

            $start = $start->plusMonths(1);

            $return[$current->format("m/Y")] = ($formatLong ? Config::get("months/long")[$monthIndex] : Config::get("months/short")[$monthIndex]) . " " . $current->format("Y");
        }

        return $return;
    }

    static public function getMonthStartAndEndDate($month, $year = null)
    {
        if (is_null($year)) $year = date("Y");

        return ['start' => Clock::at("{$year}-{$month}-01")->format("Y-m-d"), 'end' => Clock::at("{$year}-{$month}-01")->format("Y-m-t")];
    }
}
