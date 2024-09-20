<?php

namespace Helpers;

use Ouzo\Utilities\Strings;

abstract class CString
{
	public static function formatBankAccount($bankaccount, $defaultValue = null)
	{
		if (is_null($bankaccount) || strlen($bankaccount) == 0) return $defaultValue;
		return join(" ", str_split($bankaccount, 4));
	}

	public static function formatBankId($bankId, $defaultValue = null)
	{
		if (is_null($bankId) || strlen($bankId) == 0) return $defaultValue;
		return substr($bankId, 0, 4) . " " . substr($bankId, 4, 2) . " " . substr($bankId, 6, 2);
	}

	public static function formatCurrency($value, $round = 2, $prefix = "€")
	{
		return "{$prefix} " . self::formatNumber($value, $round);
	}

	public static function formatNumber($value, $round, $decimal = ",", $thousands = ".")
	{
		return number_format($value, $round, $decimal, $thousands);
	}

	public static function noHtml($value)
	{
		return strip_tags($value);
	}

	public static function noLines($value, $spacer)
	{
		$value = self::noHtml($value);
		$value = explode(PHP_EOL, $value);
		return implode($spacer, $value);
	}

	public static function firstLetterOfEachWord($value)
	{
		$floew = "";

		foreach (explode(" ", Strings::trimToNull($value)) as $v) $floew .= substr($v, 0, 1);

		return $floew;
	}
}
