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

Code::errors();
Code::noTimeLimit();
Session::Close();

$start = Clock::now();
define("_LOGTIMESTAMP_", Clock::nowAsString("Y-m-d H-i-s"));
define("_LOGLOCATION_", "cron/{$args['part']}");
define("_CURRENT_SCHOOLYEAR_", (new Schoolyear)->getCurrent()->name);

Log::Open(_LOGLOCATION_, _LOGTIMESTAMP_);
Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Schoolyear: " . _CURRENT_SCHOOLYEAR_);

$part = ucfirst($args["part"]);
$function = ucfirst($args["function"]);

$class = "\\Controllers\\API\\Cron\\{$part}";

if (!class_exists($class)) http_response_code(404);
else if (!method_exists($class, $function)) http_response_code(404);
else {
    $settingRepo = new Setting;
    $settingId = "cron.{$args['part']}.{$args['function']}.active";
    $setting = $settingRepo->getById($settingId);

    if (General::convert($setting->value, 'bool') == false) {
        $setting->value = 1;
        $settingRepo->set($setting);

        unset($args['part'], $args['function'], $args['mode']);
        if ($class::$function(...$args)) http_response_code(200);
        else http_response_code(400);

        $setting->value = 0;
        $settingRepo->set($setting);
    } else {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Instance already running!");
    }
}

$end = Clock::now();
Log::Close(_LOGLOCATION_, _LOGTIMESTAMP_, $start->getTimestamp(), $end->getTimestamp());
