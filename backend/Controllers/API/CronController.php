<?php

namespace Controllers\API;

use Controllers\ApiController;
use Ouzo\Utilities\Strings;
use Security\Code;

class CronController extends ApiController
{
    public function index($action)
    {
        Code::noTimeLimit();

        if (Strings::equalsIgnoreCase($action, "importInformat")) {
            if (\Controllers\API\Cron\Informat::import()) $this->appendToJson('importInformat', 'passed');
            else $this->appendToJson('importInformat', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "importM365")) {
            \Controllers\API\Cron\M365::import();
        }

        $this->handle();
    }
}
