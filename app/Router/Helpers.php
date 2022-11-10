<?php

namespace Router;

use Ouzo\Utilities\Arrays;
use Pecee\Http\Url;
use Pecee\Http\Request;
use Pecee\Http\Response;
use Pecee\SimpleRouter\SimpleRouter;

abstract class Helpers
{
	static function url(?string $name = null, $parameters = null, ?array $getParams = null): Url
	{
		return SimpleRouter::getUrl($name, $parameters, $getParams);
	}

	static function response(): Response
	{
		return SimpleRouter::response();
	}

	static function request(): Request
	{
		return SimpleRouter::request();
	}

	static function input($index = null, $defaultValue = null, ...$methods)
	{
		if ($index !== null) {
			return self::request()->getInputHandler()->value($index, $defaultValue, ...$methods);
		}

		return self::request()->getInputHandler();
	}

	static function redirect(string $url, ?int $code = null): void
	{
		if ($code !== null) {
			self::response()->httpCode($code);
		}

		self::response()->redirect($url);
	}

	static function csrf_token(): ?string
	{
		$baseVerifier = SimpleRouter::router()->getCsrfVerifier();
		if ($baseVerifier !== null) {
			return $baseVerifier->getTokenProvider()->getToken();
		}

		return null;
	}

	static function get_module()
	{
		$url = trim(self::url()->getRelativeUrl(false), "/");
		return Arrays::first(explode("/", $url));
	}

	static function get_page()
	{
		$url = trim(self::url()->getRelativeUrl(false), "/");
		return explode("/", $url)[1];
	}
}
