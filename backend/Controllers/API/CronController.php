<?php

namespace Controllers\API;

use Helpers\Log;
use Security\Code;
use Ouzo\Utilities\Clock;
use Controllers\ApiController;
use Database\Repository\General\Schoolyear;
use Database\Repository\Setting\Setting;
use Helpers\General;
use Security\Session;

class CronController extends ApiController
{
    public function index($part, $function)
    {
        Code::noTimeLimit();
        Session::Close();

        define("_LOGTIMESTAMP_", Clock::nowAsString("Y-m-d H-i-s"));
        define("_LOGLOCATION_", "cron/{$part}");
        define("_CURRENT_SCHOOLYEAR_", (new Schoolyear)->getCurrent()->name);

        Log::Open(_LOGLOCATION_, _LOGTIMESTAMP_);
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Schoolyear: " . _CURRENT_SCHOOLYEAR_);

        $part = ucfirst($part);
        $function = ucfirst($function);

        $class = "\\Controllers\\API\\Cron\\{$part}";

        if (!class_exists($class)) $this->setHttpCode(404);
        else if (!method_exists($class, $function)) $this->setHttpCode(404);
        else {
            $settingRepo = new Setting;
            $settingId = "cron.{$part}.{$function}.active";
            $setting = $settingRepo->getById($settingId);

            if (General::convert($setting->value, 'bool') == false) {
                $setting->value = 1;
                $settingRepo->set($setting);

                if ($class::$function()) $this->setHttpCode(200);
                else $this->setHttpCode(400);

                $setting->value = 0;
                $settingRepo->set($setting);
            } else {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Instance already running!");
            }
        }

        Log::Close(_LOGLOCATION_, _LOGTIMESTAMP_);

        $this->handle();
    }
}
