<?php

namespace Middleware;

use Controllers\API\UserController;
use Ouzo\Utilities\Arrays;
use Router\Helpers;
use Security\User;

abstract class ApiMiddleware
{
	public static function handle()
	{
		$noAuthRoutesJson = json_decode(file_get_contents(LOCATION_BACKEND . "/config/noAuthRoutes.json"), true);
		$requestMethod = strtoupper(Helpers::request()->getLoadedRoute()->getRequestMethods()[0]);

		$noAuthRoutes = $noAuthRoutesJson[$requestMethod];
		$route = rtrim(Helpers::request()->getLoadedRoute()->getUrl(), "/");

		if (!Arrays::contains($noAuthRoutes, $route)) {
			if (!User::isSignedIn()) {
				if (!UserController::apiLogin()) {
					Helpers::response()->httpCode(401);
					Helpers::response()->json(["error" => "You are not authorized!"]);
				}
			}
		}
	}
}
