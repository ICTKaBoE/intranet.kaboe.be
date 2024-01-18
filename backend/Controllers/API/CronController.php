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

class CronController extends ApiController
{
	public function index($action)
	{
		set_time_limit(0);
		$action = explode("-", strtolower($action));

		$pool = Pool::create();

		foreach ($action as $a) {
			if (Strings::equalsIgnoreCase($a, "sendMail")) $pool->add(fn () => CronMail::SendMail());
			else if (Strings::equalsIgnoreCase($a, "adSync")) $pool->add(fn () => CronAD::Sync());
			else if (Strings::equalsIgnoreCase($a, "informatSync")) $pool->add(fn () => CronInformat::Sync());
			else if (Strings::equalsIgnoreCase($a, "jamfSync")) $pool->add(fn () => CronJamf::Sync());
			else if (Strings::equalsIgnoreCase($a, "prepareSync")) $pool->add(fn () => CronPrepare::Sync());
			else if (Strings::equalsIgnoreCase($a, "localUserSync")) $pool->add(fn () => CronLocal::UserSync());
		}

		$pool->wait();
		$this->handle();
	}
}
