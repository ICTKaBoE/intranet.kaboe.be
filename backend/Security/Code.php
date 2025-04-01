<?php

namespace Security;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting\Setting;

abstract class Code
{
	static public function errors($onOff = false)
	{
		if ($onOff) {
			error_reporting(E_ERROR);
			ini_set('display_errors', 'On');
		} else {
			error_reporting(0);
			ini_set('display_errors', 'Off');
		}
	}

	static public function noTimeLimit()
	{
		ignore_user_abort(true);
		set_time_limit(0);
	}

	static public function CheckDatabaseVersion()
	{
		$dbVersion = Arrays::firstOrNull((new Setting)->get(id: "db.version"))->value;
		return Strings::equal($dbVersion, VERSION_DB);
	}
}
