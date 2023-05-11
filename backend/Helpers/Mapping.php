<?php

namespace Helpers;

use Ouzo\Utilities\Arrays;

abstract class Mapping
{
	static public function get($key)
	{
		if (is_string($key)) $key = explode("/", $key);

		$mappings = json_decode(file_get_contents(LOCATION_BACKEND . "/config/mappings.json"), true);
		return Arrays::getNestedValue($mappings, $key) ?? null;
	}
}
