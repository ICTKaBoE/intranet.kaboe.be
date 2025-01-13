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

	public static function formatMacAddress($value, $inBetween = ":")
	{
		return join($inBetween, str_split(str_replace([":", "-", " "], "", $value), 2));
	}

	public static function formatLink($value)
	{
		return "<a href='https://{$value}' target='_blank'>$value</a>";
	}

	public static function formatPassword($value)
	{
		$ret = "";
		for ($i = 0; $i < strlen($value); $i++) $ret .= "*";

		return $ret;
	}

	public static function formatAddress($street = null, $number = null, $bus = null, $zipcode = null, $city = null, $country = null)
	{
		return "{$street} {$number}" . ($bus ? "/{$bus}" : "") . ", {$zipcode} {$city}, {$country}";
	}

	public static function leadingZeros($value, $fullLength)
	{
		return sprintf("%0{$fullLength}d", $value);
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

	public static function getStringBetween($string, $startChar, $endChar)
	{
		$pattern = "/" . preg_quote($startChar, '/') . "(.*?)" . preg_quote($endChar, '/') . "/";
		preg_match($pattern, $string, $matches);
		return $matches[1] ?? '';
	}
}
