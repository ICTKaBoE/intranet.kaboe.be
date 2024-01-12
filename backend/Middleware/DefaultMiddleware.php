<?php

namespace Middleware;

use Router\Helpers;
use Pecee\Http\Request;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Pecee\Http\Middleware\IMiddleware;
use Security\User;

abstract class DefaultMiddleware
{
	public static function handle(): void
	{
		if (self::checkStopRedirection()) {
			self::checkUserCanAccess();
			self::checkFileExistance();
		}
	}

	static private function checkStopRedirection()
	{
		$stopRedirectionJson = json_decode(file_get_contents(LOCATION_BACKEND . "/config/stopRedirectionRoutes.json"), true);
		$requestMethod = strtoupper(Helpers::request()->getLoadedRoute()->getRequestMethods()[0]);

		$stopRedirection = $stopRedirectionJson[$requestMethod];
		$route = rtrim(Helpers::request()->getLoadedRoute()->getUrl(), "/");

		if (Arrays::contains($stopRedirection, $route)) return false;
		return true;
	}

	static private function checkUserCanAccess()
	{
		$folder = Helpers::getReletiveUrl();
		$defaultPage = (new Setting)->get(id: (User::isSignedIn() ? "page.default.afterLogin" : "page.default.public"))[0]->value;

		if (Helpers::isPublicPage()) {
			if (Strings::isBlank($folder) || Arrays::contains(["/error", "/public", "/app"], $folder)) Helpers::redirect($defaultPage);
		} else {
			if (!User::isSignedIn() && !Strings::equal($folder, $defaultPage)) Helpers::redirect($defaultPage);
			else if (User::isSignedIn() && (Strings::isBlank($folder) || Arrays::contains(["/error", "/public", "/app"], $folder))) Helpers::redirect($defaultPage);
		}
	}

	static private function checkFileExistance()
	{
		$folder = Helpers::getReletiveUrl();
		if (!file_exists(LOCATION_FRONTEND . $folder)) Helpers::redirect("/error/404", 404);
	}
}
