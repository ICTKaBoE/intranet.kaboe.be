<?php

namespace Helpers;

use Ouzo\Utilities\Clock;
use Security\FileSystem;

abstract class Log
{
    static public function Open($location, $timestamp)
    {
        $now = Clock::nowAsString("Y-m-d H:i:s");
        FileSystem::CreateFolder(LOCATION_LOGS . "/{$location}");
        self::Write($location, $timestamp, "INFO", "BEGIN OF LOG...");
    }

    static public function Close($location, $timestamp, $start = null, $end = null)
    {
        self::EmptyLine($location, $timestamp);
        self::Write($location, $timestamp, "INFO", "END OF LOG...");

        if ($start && $end) {
            $diff = abs($end - $start);
            $years = floor($diff / (365 * 60 * 60 * 24));
            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
            $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
            $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
            $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));

            self::Write($location, $timestamp, "INFO", "DURATION: " . sprintf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds));
        }
    }

    static public function Write($location, $timestamp, $type, $text)
    {
        $now = Clock::nowAsString("Y-m-d H:i:s");
        FileSystem::WriteFile(LOCATION_LOGS . "/{$location}/{$timestamp}.log", "{$now}\t[{$type}]\t{$text}\n", true);
    }

    static public function EmptyLine($location, $timestamp)
    {
        FileSystem::WriteFile(LOCATION_LOGS . "/{$location}/{$timestamp}.log", "\n", true);
    }
}
