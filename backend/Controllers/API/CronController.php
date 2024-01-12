<?php

namespace Controllers\API;

use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Cron\CronInformat;
use Cron\CronJamf;
use Cron\CronLocal;
use Cron\CronMail;
use Cron\CronPrepare;
use Spatie\Async\Pool;

class CronController extends ApiController
{
	public function index($action)
	{
		set_time_limit(0);
		$action = explode("-", strtolower($action));

		$pool = Pool::create();

		foreach ($action as $a) {
			if (Strings::equalsIgnoreCase($a, "sendMail")) $pool->add(fn () => CronMail::SendMail());
			else if (Strings::equalsIgnoreCase($a, "informatSync")) $pool->add(fn () => CronInformat::Sync());
			else if (Strings::equalsIgnoreCase($a, "jamfSync")) $pool->add(fn () => CronJamf::Sync());
			else if (Strings::equalsIgnoreCase($a, "prepareSync")) $pool->add(fn () => CronPrepare::Sync());
			else if (Strings::equalsIgnoreCase($a, "localUserSync")) $pool->add(fn () => CronLocal::UserSync());
		}

		$pool->wait();
		$this->handle();
	}
}
