<?php

use Database\Repository\Cron;
use Helpers\Log;
use Security\Code;
use Helpers\General;
use Ouzo\Utilities\Clock;
use Database\Repository\Setting\Setting;
use Database\Repository\General\Schoolyear;

require_once __DIR__ . "/backend/autoload.php";
parse_str(implode('&', array_slice($argv, 1)), $args);

Code::errors(true);
Code::noTimeLimit();

$start = Clock::now();
$busy = false;
$class = ucfirst($args["class"]);
$function = ucfirst($args["function"]);

$cronRepo = new Cron;
$cron = $cronRepo->getByClassAndFunction($args['class'], $args['function']);
$cron->lastRun = $start->format("Y-m-d H:i:s");
$cronRepo->set($cron);

define("_LOGTIMESTAMP_", Clock::nowAsString("Y-m-d H-i-s"));
define("_LOGLOCATION_", "cron/{$args['class']}/{$args['function']}/" . Clock::nowAsString("Y-m-d"));
define("_CURRENT_SCHOOLYEAR_", (new Schoolyear)->getCurrent()->name);

Log::Open(_LOGLOCATION_, _LOGTIMESTAMP_);
Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Schoolyear: " . _CURRENT_SCHOOLYEAR_);

if ((int)Clock::now()->format("H") >= 22 && (int)Clock::now()->format("H") <= 4) {
    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "Instance not allowed to run!");
} else {
    $settingRepo = new Setting;
    $mode = $args['mode'];

    $class = "\\Controllers\\Cron\\{$class}";

    if (class_exists($class) && method_exists($class, $function)) {
        if (!$cron->active) {
            if (!isset($mode)) {
                $cron->active = 1;
                $cronRepo->set($cron);
            }

            unset($args['class'], $args['function'], $args['mode']);

            try {
                $result = $class::$function(...$args);
            } catch (\Exception $e) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $e->getMessage());
            } finally {
                if (!isset($mode)) {
                    $cron->active = 0;
                    $cronRepo->set($cron);
                }
            }
        } else {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Instance already running!");
            $busy = true;
        }
    }
}

$end = Clock::now();
Log::Close(_LOGLOCATION_, _LOGTIMESTAMP_, $start->getTimestamp(), $end->getTimestamp());
echo $start->format("Y-m-d H:i:s") . "\t[" . ($busy ? "BUSY" : ($result ? "PASSED" : "FAILED")) . "]\t{$class} - {$function}\n";
