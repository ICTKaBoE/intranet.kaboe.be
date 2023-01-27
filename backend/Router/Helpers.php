<?php

namespace Router;

use Pecee\Http\Url;
use Pecee\Http\Request;
use Pecee\Http\Response;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Strings;
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

	static function getPrefix()
	{
		if (!is_null(self::request()->getLoadedRoute())) return self::request()->getLoadedRoute()->getGroup()->getPrefix();

		$url = trim(self::url()->getRelativeUrl(false), "/");
		return "/" . explode("/", $url)[0];
	}

	static function getModule()
	{
		if (!is_null(self::request()->getLoadedRoute()) && Arrays::keyExists(self::request()->getLoadedRoute()->getParameters(), "module")) return self::request()->getLoadedRoute()->getParameters()['module'];

		$url = trim(self::url()->getRelativeUrl(false), "/");
		return explode("/", $url)[1] ?? false;
	}

	static function getPage()
	{
		if (!is_null(self::request()->getLoadedRoute()) && Arrays::keyExists(self::request()->getLoadedRoute()->getParameters(), "page")) return self::request()->getLoadedRoute()->getParameters()['page'];

		$url = trim(self::url()->getRelativeUrl(false), "/");
		return explode("/", $url)[2] ?? false;
	}

	static function getMethod()
	{
		if (!is_null(self::request()->getLoadedRoute()) && Arrays::keyExists(self::request()->getLoadedRoute()->getParameters(), "method")) return self::request()->getLoadedRoute()->getParameters()['method'];

		$url = trim(self::url()->getRelativeUrl(false), "/");
		return explode("/", $url)[3] ?? false;
	}

	static function getId()
	{
		if (!is_null(self::request()->getLoadedRoute()) && Arrays::keyExists(self::request()->getLoadedRoute()->getParameters(), "id")) return self::request()->getLoadedRoute()->getParameters()['id'];

		$url = trim(self::url()->getRelativeUrl(false), "/");
		return explode("/", $url)[4] ?? false;
	}

	static function isPublicPage()
	{
		return Strings::equal(self::getPrefix(), "/public");
	}

	static function isErrorPage()
	{
		return Strings::equal(self::getModule(), 'error');
	}

	static function getPageFolder()
	{
		return Path::normalize(self::getPrefix() . "/" . self::getModule() . (self::getPage() ? "/" . self::getPage() : "") . (self::getMethod() ? (self::isPublicPage() ? "" : "/form") : ""));
	}

	static function getApiPath()
	{
		return Path::normalize(self::getPrefix() . "/" . self::getModule() . (self::getPage() ? "/" . self::getPage() : "") . (self::getMethod() ? (self::isPublicPage() ? "" : "/" . self::getMethod()) : "") . (self::getId() ? "/" . self::getId() : ""));
	}
}
