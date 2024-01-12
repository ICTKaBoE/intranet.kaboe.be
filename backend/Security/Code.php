<?php

namespace Security;

abstract class Code
{
	static public function showErrors()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
	}
}
