<?php

namespace Router;

use Pecee\Http\Url;
use Pecee\Http\Request;
use Pecee\Http\Response;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Strings;
use Pecee\SimpleRouter\SimpleRouter;
use Security\FileSystem;

abstract class Helpers
{
	static $noAuthRoutes = [];

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

	static function getReletiveUrl()
	{
		return rtrim(self::url()->getRelativeUrl(false), "/");
	}

	static function getPrefix()
	{
		return (self::isErrorPage() ? "error" : (self::isPublicPage() ? "public" : "app"));
	}

	static function getModule()
	{
		if (!is_null(self::request()->getLoadedRoute()) && Arrays::keyExists(self::request()->getLoadedRoute()->getParameters(), "module")) return self::request()->getLoadedRoute()->getParameters()['module'];

		$route = explode("/", ltrim(self::getReletiveUrl(), "/"));
		return $route[1];
	}

	static function getPage()
	{
		if (!is_null(self::request()->getLoadedRoute()) && Arrays::keyExists(self::request()->getLoadedRoute()->getParameters(), "page")) return self::request()->getLoadedRoute()->getParameters()['page'];

		$route = explode("/", ltrim(self::getReletiveUrl(), "/"));
		return $route[2];
	}

	static function isPublicPage()
	{
		return Strings::startsWith(self::getReletiveUrl(), "/public");
	}

	static function isErrorPage()
	{
		return Strings::startsWith(self::getReletiveUrl(), "/error");
	}

	static function registerNoAuthRoute($method, $route)
	{
		$noAuthRoutes = json_decode(file_get_contents(LOCATION_BACKEND . "/config/noAuthRoutes.json"), true);
		if (Arrays::contains($noAuthRoutes[$method], $route)) return;

		$noAuthRoutes[$method][] = $route;
		file_put_contents(LOCATION_BACKEND . "/config/noAuthRoutes.json", json_encode($noAuthRoutes));
	}

	static function registerStopRedirectionRoute($method, $route)
	{
		$stopRedirectionRoutes = json_decode(file_get_contents(LOCATION_BACKEND . "/config/stopRedirectionRoutes.json"), true);
		if (Arrays::contains($stopRedirectionRoutes[$method], $route)) return;

		$stopRedirectionRoutes[$method][] = $route;
		file_put_contents(LOCATION_BACKEND . "/config/stopRedirectionRoutes.json", json_encode($stopRedirectionRoutes));
	}
}
