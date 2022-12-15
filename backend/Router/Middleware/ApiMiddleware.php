<?php

namespace Router\Middleware;

use Ouzo\Utilities\Arrays;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Router\Helpers;
use Security\User;

class ApiMiddleware implements IMiddleware
{
	const SKIP_SIGN_IN_CHECK = [
		"/api/v1.0/user/sync/",
		"/api/v1.0/app/user/login/",
		"/api/v1.0/user/o365/callback/",
		"/api/v1.0/form/app/user/login/"
	];

	public function handle(Request $request): void
	{
		if (!Arrays::contains(self::SKIP_SIGN_IN_CHECK, Helpers::url()->getRelativeUrl(false))) {
			if (!User::isSignedIn()) {
				Helpers::response()->httpCode(401);
				Helpers::response()->json(["error" => "You are not authorized!"]);
			} else {
				$request->authenticated = true;
			}
		} else {
			$request->authenticated = true;
		}
	}
}
