<?php

namespace Security;

abstract class Code
{
	static public function errors($onOff = false)
	{
		if ($onOff) {
			error_reporting(E_ALL);
			ini_set('display_errors', 'On');
		} else {
			error_reporting(0);
			ini_set('display_errors', 'Off');
		}
	}

	static public function noTimeLimit()
	{
		set_time_limit(0);
	}
}
