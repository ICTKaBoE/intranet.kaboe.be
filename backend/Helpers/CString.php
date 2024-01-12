<?php

namespace Helpers;

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

	public static function formatCurrency($value, $round = 2)
	{
		return "€ " . number_format((int)$value, $round, ",", ".");
	}

	public static function noHtml($value)
	{
		return strip_tags($value);
	}
}
