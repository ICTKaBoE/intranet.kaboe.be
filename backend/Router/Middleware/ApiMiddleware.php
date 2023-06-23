<?php

namespace Router\Middleware;

use Controllers\API\UserController;
use Ouzo\Utilities\Arrays;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Router\Helpers;
use Security\User;

class ApiMiddleware implements IMiddleware
{
	const SKIP_SIGN_IN_CHECK = [
		"/api/v1.0/clean/",
		"/api/v1.0/user/sync/",
		"/api/v1.0/sync/ad/user/",
		"/api/v1.0/sync/informat/",
		"/api/v1.0/app/user/login/",
		"/api/v1.0/user/o365/callback/",
		"/api/v1.0/form/app/user/login/",
		"/api/v1.0/check/student/relation/"
	];

	public function handle(Request $request): void
	{
		if (!Arrays::contains(self::SKIP_SIGN_IN_CHECK, Helpers::url()->getRelativeUrl(false))) {
			if (!User::isSignedIn()) {
				if (!(new UserController)->login(prefix: null, apiLogin: true)) {
					Helpers::response()->httpCode(401);
					Helpers::response()->json(["error" => "You are not authorized!"]);
				}
			} else {
				$request->authenticated = true;
			}
		} else {
			$request->authenticated = true;
		}
	}
}
