<?php

namespace Security;

use Ouzo\Utilities\Arrays;
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
	const INPUT_TYPE_INSZ = 'insz';

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
			case self::INPUT_TYPE_INSZ:
				return self::checkInsz($input);
				break;

			default:
				return filter_var($input);
		}
	}

	static public function empty($input)
	{
		if (self::check($input, self::INPUT_TYPE_ARRAY)) return empty($input);
		if (self::check($input, self::INPUT_TYPE_INT) !== false && (int)$input == 0) return false;

		return Strings::isBlank($input) || is_null($input) || empty($input);
	}

	static public function isEmail($input)
	{
		return Strings::contains($input, "@");
	}

	static public function checkInsz($input)
	{
		if (self::empty($input)) return true;
		$input = preg_replace('/[^0-9]/', "", $input);

		$checksum = (int)substr($input, 9, 2);
		$calculationPart = (int)substr($input, 0, 9);

		$expectedChecksum = 97 - ($calculationPart % 97);
		if ($expectedChecksum == $checksum) return true;

		$calculationPart = (int)"2{$calculationPart}";

		$expectedChecksum = 97 - ($calculationPart % 97);
		if ($expectedChecksum == $checksum) return true;
		return false;
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

	static public function convertToBool($input)
	{
		if (Strings::equal($input, "on")) return true;
		else if (Strings::equal($input, "off")) return false;
		else if (Strings::equal($input, "true")) return true;
		else if (Strings::equal($input, "false")) return false;
		else if (Strings::equal($input, "1")) return true;
		else if (Strings::equal($input, "0")) return false;

		return $input;
	}

	static public function formatInsz($input)
	{
		$input = preg_replace('/[^0-9]/', "", $input);
		if (Strings::isBlank($input)) return "";

		return substr($input, 0, 2) . "." . substr($input, 2, 2) . "." . substr($input, 4, 2) . "-" . substr($input, 6, 3) . "." . substr($input, 9, 2);
	}

	static public function convertArrayToCsv($array, $delimiter = ";")
	{
		$csv = "";
		$array = array_values($array);
		$keys = $array[0]->getKeys();

		$csv = implode($delimiter, $keys) . PHP_EOL;

		foreach ($array as $row) {
			$row = (array)$row;
			$row = Arrays::filterByAllowedKeys($row, $keys);

			$csv .= implode($delimiter, $row) . PHP_EOL;
		}

		return $csv;
	}

	static public function clean($input)
	{
		$utf8 = array(
			'/[áàâãªä]/u'   =>   'a',
			'/[ÁÀÂÃÄ]/u'    =>   'A',
			'/[ÍÌÎÏ]/u'     =>   'I',
			'/[íìîï]/u'     =>   'i',
			'/[éèêë]/u'     =>   'e',
			'/[ÉÈÊË]/u'     =>   'E',
			'/[óòôõºö]/u'   =>   'o',
			'/[ÓÒÔÕÖ]/u'    =>   'O',
			'/[úùûü]/u'     =>   'u',
			'/[ÚÙÛÜ]/u'     =>   'U',
			'/ç/'           =>   'c',
			'/Ç/'           =>   'C',
			'/ñ/'           =>   'n',
			'/Ñ/'           =>   'N',
			'/–/'           =>   '', // UTF-8 hyphen to "normal" hyphen
			'/[’‘‹›‚]/u'    =>   '', // Literally a single quote
			'/[“”«»„]/u'    =>   '', // Double quote
			'/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
		);
		return preg_replace(array_keys($utf8), array_values($utf8), $input);
	}
}
