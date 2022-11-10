<?php

namespace Security;

abstract class Input
{
	const INPUT_TYPE_STRING = 'string';
	const INPUT_TYPE_EMAIL = 'email';
	const INPUT_TYPE_BOOL = 'bool';
	const INPUT_TYPE_DOMAIN = 'domain';
	const INPUT_TYPE_FLOAT = 'float';
	const INPUT_TYPE_INT = 'int';
	const INPUT_TYPE_IP = 'ip';
	const INPUT_TYPE_MAC = 'mac';
	const INPUT_TYPE_REGEXP = 'regexp';
	const INPUT_TYPE_URL = 'url';

	static public function check($input, $type = self::INPUT_TYPE_STRING)
	{
		switch ($type) {
			case self::INPUT_TYPE_EMAIL:
				return filter_var($input, FILTER_VALIDATE_EMAIL);
				break;

			default:
				return filter_var($input);
		}
	}

	static public function sanitize($input, $type = self::INPUT_TYPE_STRING)
	{
		switch ($type) {
			case self::INPUT_TYPE_EMAIL:
				$input = filter_var($input, FILTER_SANITIZE_EMAIL);
				break;

			default:
				$input = $input;
		}

		return htmlentities($input);
	}
}
