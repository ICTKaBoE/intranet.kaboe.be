<?php

namespace Controllers\API;

use Cron\CronAD;
use Cron\CronJamf;
use Cron\CronMail;
use Cron\CronLocal;
use Cron\CronPrepare;
use Cron\CronInformat;
use Spatie\Async\Pool;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Security\Code;
use Throwable;

class CronController extends ApiController
{
	public function index($action)
	{
		try {
			// Code::showErrors();
			set_time_limit(0);
			$action = explode("-", strtolower($action));

			foreach ($action as $a) {
				if (Strings::equalsIgnoreCase($a, "sendMail")) CronMail::SendMail();
				else if (Strings::equalsIgnoreCase($a, "adSync")) CronAD::Sync();
				else if (Strings::equalsIgnoreCase($a, "informatSync")) CronInformat::Sync();
				else if (Strings::equalsIgnoreCase($a, "jamfSync")) CronJamf::Sync();
				else if (Strings::equalsIgnoreCase($a, "prepareSync")) CronPrepare::Sync();
				else if (Strings::equalsIgnoreCase($a, "localUserSync")) CronLocal::UserSync();
			}
		} catch (\Exception $e) {
			die(var_dump($e->getMessage()));
		}

		$this->handle();
	}
}
