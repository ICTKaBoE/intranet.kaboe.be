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

        if (Strings::equalsIgnoreCase($action, "importCountries")) {
            if (\Controllers\API\Cron\Country::Import()) $this->appendToJson('importCountries', 'passed');
            else $this->appendToJson('importCountries', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "importInformat")) {
            if (\Controllers\API\Cron\Informat::Import()) $this->appendToJson('importInformat', 'passed');
            else $this->appendToJson('importInformat', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "importM365Users")) {
            if (\Controllers\API\Cron\M365::ImportUsers()) $this->appendToJson('importM365Users', 'passed');
            else $this->appendToJson('importM365Users', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "importM365Computers")) {
            if (\Controllers\API\Cron\M365::ImportComputers()) $this->appendToJson('importM365Computers', 'passed');
            else $this->appendToJson('importM365Computers', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "importM365SignIns")) {
            if (\Controllers\API\Cron\M365::ImportSignInTimes()) $this->appendToJson('importM365SignIns', 'passed');
            else $this->appendToJson('importM365SignIns', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "local")) {
            if (\Controllers\API\Cron\Local::Prepare()) $this->appendToJson('local', 'passed');
            else $this->appendToJson('local', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "importJamfIpads")) {
            if (\Controllers\API\Cron\JAMF::ImportIPads()) $this->appendToJson('importJamfIpads', 'passed');
            else $this->appendToJson('importJamfIpads', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "sync")) {
            if (\Controllers\API\Cron\Sync::Prepare()) $this->appendToJson('sync', 'passed');
            else $this->appendToJson('sync', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "sendMails")) {
            if (\Controllers\API\Cron\Mail::Send()) $this->appendToJson('sendMails', 'passed');
            else $this->appendToJson('sendMails', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "m365SyncClassTeams")) {
            if (\Controllers\API\Cron\M365::SyncClassTeams()) $this->appendToJson('m365SyncClassTeams', 'passed');
            else $this->appendToJson('m365SyncClassTeams', 'failed');
        }

        if (Strings::equalsIgnoreCase($action, "m365SyncSchoolTeams")) {
            if (\Controllers\API\Cron\M365::SyncSchoolTeams()) $this->appendToJson('m365SyncSchoolTeams', 'passed');
            else $this->appendToJson('m365SyncSchoolTeams', 'failed');
        }

        $this->handle();
    }
}
