<?php

namespace Security;

use Ouzo\Utilities\Strings;

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
	const INPUT_TYPE_ARRAY = 'array';
	const INPUT_TYPE_OBJECT = 'object';

	static public function check($input, $type = self::INPUT_TYPE_STRING)
	{
		switch ($type) {
			case self::INPUT_TYPE_EMAIL:
				return filter_var($input, FILTER_VALIDATE_EMAIL);
				break;
			case self::INPUT_TYPE_INT:
				return filter_var($input, FILTER_VALIDATE_INT);
				break;
			case self::INPUT_TYPE_ARRAY:
				return is_array($input);
				break;
			case self::INPUT_TYPE_OBJECT:
				return is_object($input);
				break;

			default:
				return filter_var($input);
		}
	}

	static public function empty($input)
	{
		if (self::check($input, self::INPUT_TYPE_ARRAY)) return empty($input);

		return Strings::isBlank($input) || is_null($input) || empty($input);
	}

	static public function isEmail($input)
	{
		return Strings::contains($input, "@");
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

	static public function convertToBool($value)
	{
		if (Strings::equal($value, "on")) return true;
		else if (Strings::equal($value, "off")) return false;
		else if (Strings::equal($value, "true")) return true;
		else if (Strings::equal($value, "false")) return false;
		else if (Strings::equal($value, "1")) return true;
		else if (Strings::equal($value, "0")) return false;

		return $value;
	}

	static public function formatInsz($value)
	{
		$value = str_replace([".", "-"], "", $value);
		if (Strings::isBlank($value)) return "";

		return substr($value, 0, 2) . "." . substr($value, 2, 2) . "." . substr($value, 4, 2) . "-" . substr($value, 6, 3) . "." . substr($value, 9, 2);
	}
}
