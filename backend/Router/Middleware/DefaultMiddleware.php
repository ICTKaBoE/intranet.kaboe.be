<?php

namespace Router\Middleware;

use Router\Helpers;
use Pecee\Http\Request;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Pecee\Http\Middleware\IMiddleware;
use Security\User;

class DefaultMiddleware implements IMiddleware
{
	const STOP_REDIRECTION = [
		"/app/error/404",
		"/app/user/login",
		"/app/user/o365/login",
		"/public/error/404"
	];

	public function handle(Request $request): void
	{
		if ($this->routeIsAccessable()) {
			$this->checkUserCanAccess();
			$this->checkFileExistance();
		}
	}

	static private function routeIsAccessable()
	{
		$folder = Helpers::getPageFolder();
		$method = Helpers::getMethod();

		if (Arrays::contains(self::STOP_REDIRECTION, $folder) || Arrays::contains(self::STOP_REDIRECTION, $folder . "/" . $method)) return false;
		return true;
	}

	static private function checkUserCanAccess()
	{
		$folder = Helpers::getPageFolder();

		$loginPage = (new Setting)->get(id: "page.login")[0]->value;
		$defaultPage = (new Setting)->get(id: (Helpers::isPublicPage() ? "page.default.public" : "page.default.afterLogin"))[0]->value;

		if (Helpers::isPublicPage()) {
			if (Strings::isBlank($folder) || Strings::equal($folder, Helpers::getPrefix())) Helpers::redirect($defaultPage);
		} else {
			if (!User::isSignedIn() && !Strings::equal($folder, $loginPage)) Helpers::redirect($loginPage);
			else if (User::isSignedIn() && (Strings::isBlank($folder) || Strings::equal($folder, $loginPage) || Strings::equal($folder, Helpers::getPrefix()))) Helpers::redirect($defaultPage);
		}
	}

	static private function checkFileExistance()
	{
		$folder = Helpers::getPageFolder();

		if (!file_exists(LOCATION_FRONTEND . $folder)) Helpers::redirect(Helpers::getPrefix() . "/error/404", 404);
	}
}
