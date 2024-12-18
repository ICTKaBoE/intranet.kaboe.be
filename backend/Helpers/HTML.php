<?php

namespace Helpers;

use DOMDocument;
use DOMElement;

abstract class HTML
{
	const LINK_TYPE_URL = "url";
	const LINK_TYPE_PHONE = "tel";
	const LINK_TYPE_EMAIL = "mail";

	const LINK_TARGET_SELF = "_self";
	const LINK_TARGET_BLANK = "_blank";

	static public function Icon($icon, $title = null, $color = null, $class = [], $style = [])
	{
		$classes = ["ti", "icon", ...$class];
		$styles = [];

		if ($icon) $classes[] = "ti-{$icon}";
		if ($color) $classes[] = "text-{$color}";
		if ($style) foreach ($style as $key => $value) $styles[] = "{$key}: {$value}";

		$dom = new DOMDocument;
		$el = $dom->createElement("i");
		if (!empty($classes)) $el->setAttribute("class", implode(" ", $classes));
		if (!empty($styles)) $el->setAttribute("style", implode("; ", $styles));
		if ($title) $el->setAttribute("title", $title);

		$el->normalize();
		$dom->appendChild($el);
		return $el->ownerDocument->saveHTML($el);
	}


	static public function Badge($text, $textColor = "white", $backgroundColor = null, $class = [], $style = [])
	{
		$classes = ["badge", ...$class];
		$styles = [];

		if ($textColor) $classes[] = "text-{$textColor}";
		if ($backgroundColor) $classes[] = "bg-{$backgroundColor}";
		if ($style) foreach ($style as $key => $value) $styles[] = "{$key}: {$value}";

		$dom = new DOMDocument;
		$el = $dom->createElement("span", $text);
		if (!empty($classes)) $el->setAttribute("class", implode(" ", $classes));
		if (!empty($styles)) $el->setAttribute("style", implode("; ", $styles));

		$el->normalize();
		$dom->appendChild($el);
		return $el->ownerDocument->saveHTML($el);
	}

	static public function Link($type, $value, $text, $target = self::LINK_TARGET_SELF)
	{
		$dom = new DOMDocument;
		$el = $dom->createElement("a", $text);

		switch ($type) {
			case self::LINK_TYPE_URL: {
					$el->setAttribute("href", $value);
					$el->setAttribute("target", $target);
				}
				break;
			case self::LINK_TYPE_EMAIL: {
					$el->setAttribute("href", "mailto:{$value}");
				}
				break;
			case self::LINK_TYPE_PHONE: {
					$el->setAttribute("href", "tel:{$value}");
				}
				break;
		}

		$el->normalize();
		$dom->appendChild($el);
		return $el->ownerDocument->saveHTML($el);
	}
}
