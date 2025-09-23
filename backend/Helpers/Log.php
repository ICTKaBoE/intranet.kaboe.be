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

    static public function Close($location, $timestamp)
    {
        self::Write($location, $timestamp, "INFO", "END OF LOG...");
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
