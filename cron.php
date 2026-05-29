<?php

use Helpers\Log;
use Security\Code;
use Helpers\General;
use Security\Session;
use Ouzo\Utilities\Clock;
use Database\Repository\Setting\Setting;
use Database\Repository\General\Schoolyear;

require_once __DIR__ . "/backend/autoload.php";
parse_str(implode('&', array_slice($argv, 1)), $args);

Code::errors(true);
Code::noTimeLimit();

$start = Clock::now();
define("_LOGTIMESTAMP_", Clock::nowAsString("Y-m-d H-i-s"));
define("_LOGLOCATION_", "cron/{$args['part']}/{$args['function']}");
define("_CURRENT_SCHOOLYEAR_", (new Schoolyear)->getCurrent()->name);

Log::Open(_LOGLOCATION_, _LOGTIMESTAMP_);
Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Schoolyear: " . _CURRENT_SCHOOLYEAR_);

$part = ucfirst($args["part"]);
$function = ucfirst($args["function"]);
$mode = $args['mode'];

$class = "\\Controllers\\Cron\\{$part}";

if (class_exists($class) && method_exists($class, $function)) {
    $settingRepo = new Setting;
    $settingId = "cron.{$args['part']}.{$args['function']}.active";
    $setting = $settingRepo->getById($settingId);
    $busy = false;

    if (General::convert($setting->value, 'bool') == false) {
        if (!isset($mode)) {
            $setting->value = 1;
            $settingRepo->set($setting);
        }

        unset($args['part'], $args['function'], $args['mode']);

        try {
            $result = $class::$function(...$args);
        } catch (\Exception $e) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $e->getMessage());
        } finally {
            if (!isset($mode)) {
                $setting->value = 0;
                $settingRepo->set($setting);
            }
        }
    } else {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Instance already running!");
        $busy = true;
    }
}

$end = Clock::now();
Log::Close(_LOGLOCATION_, _LOGTIMESTAMP_, $start->getTimestamp(), $end->getTimestamp());
echo $start->format("Y-m-d H:i:s") . "\t[" . ($busy ? "BUSY" : ($result ? "PASSED" : "FAILED")) . "]\t{$part} - {$function}\n";
