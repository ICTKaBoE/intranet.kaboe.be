<?php

namespace Security;

use DOMDocument;

abstract class Icon
{
	static public function load($name, $add = [])
	{
		$html = file_get_contents(LOCATION_ICON . "{$name}.svg");
		$dom = new DOMDocument();
		$dom->loadHTML($html);

		$svg = $dom->getElementsByTagName('svg')[0];

		if (count($add)) {
			foreach ($add as $key => $value) {
				if (is_array($value)) foreach ($value as $v) $svg->setAttribute($key, $svg->getAttribute($key) . " " . $v);
				else $svg->setAttribute($key, $svg->getAttribute($key) . " " . $value);
			}
		}

		return $dom->saveHTML();
	}
}
