<?php

namespace Router;

use Pecee\Http\Url;
use Pecee\Http\Request;
use Ouzo\Utilities\Path;
use Pecee\Http\Response;
use Security\FileSystem;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Pecee\SimpleRouter\SimpleRouter;

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

	static function getReletiveUrl($params = false)
	{
		return rtrim(self::url()->getRelativeUrl($params), "/");
	}

	static function getDomainFolder()
	{
		$host = self::url()->getHost();

		$mode = (new Setting)->get("site.mode")[0]->value;
		$mainUrl = (new Setting)->get("site.mainUrl")[0]->value;

		$host = str_replace([$mode, $mainUrl, "."], "", $host);

		return $host;
	}

	static function getModule()
	{
		return Arrays::getValue(self::request()->getLoadedRoute()->getParameters(), "module");
	}

	static function getPage()
	{
		return Arrays::getValue(self::request()->getLoadedRoute()->getParameters(), "page");
	}

	static function getId()
	{
		return Arrays::getValue(self::request()->getLoadedRoute()->getParameters(), "id");
	}

	static function getDirectory()
	{
		if (!self::getModule()) return "/" . (self::getDomainFolder() ?: "public") . self::getReletiveUrl();
		else return "/" . (self::getDomainFolder() ?: "public") . "/" . self::getModule() . (self::getPage() ? "/" . self::getPage() : "") . (self::getId() ? "/form" : "");
	}
}
