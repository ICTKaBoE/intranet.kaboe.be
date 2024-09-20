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
            if (\Controllers\API\Cron\Informat::Import()) $this->appendToJson('importInformat', 'passed');
            else $this->appendToJson('importInformat', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "importM365")) {
            \Controllers\API\Cron\M365::Import();
        }

        if (Strings::equalsIgnoreCase($action, "local")) {
            if (\Controllers\API\Cron\Local::Prepare()) $this->appendToJson('local', 'passed');
            else $this->appendToJson('local', 'failed');
        }

        $this->handle();
    }
}
