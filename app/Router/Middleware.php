<?php

namespace Router;

use Security\User;
use Pecee\Http\Request;
use Ouzo\Utilities\Arrays;
use Database\Repository\Setting;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Strings;
use Pecee\Http\Middleware\IMiddleware;

class Middleware implements IMiddleware
{
	const STOP_REDIRECTION = [
		'/user/login',
		'/user/forgot_password',
		'/error/404',
		'/o365/callback'
	];

	public function handle(Request $request): void
	{
		self::checkRouteAccessibility();
		self::checkRouteWrongPoint();
		self::checkRouteAvailability();
	}

	static private function checkRouteWrongPoint()
	{
		$url = rtrim(Helpers::url()->getRelativeUrl(false), "/");
		$loginPage = (new Setting)->get(id: "page.login")[0]->value;

		if (!User::isSignedIn() && !Strings::equal($url, $loginPage)) Helpers::redirect($loginPage);
		else if (User::isSignedIn()) {
			if (Strings::isBlank($url)) Helpers::redirect((new Setting)->get(id: "page.default.afterLogin")[0]->value);
		}
	}

	static private function checkRouteAccessibility()
	{
		$url = rtrim(Helpers::url()->getRelativeUrl(false), "/");

		if (Arrays::contains(self::STOP_REDIRECTION, $url)) return;
	}

	static private function checkRouteAvailability()
	{
		$url = rtrim(Helpers::url()->getRelativeUrl(false), "/");

		if (!file_exists(LOCATION_PUBLIC . "/Pages{$url}/index.php")) Helpers::redirect("/error/404", 404);
	}
}
