<?php

namespace Router;

use Pecee\SimpleRouter\SimpleRouter;

class Router extends SimpleRouter
{
	public static function start($debug = false): void
	{
		self::createRoutes();

		try {
			if ($debug) {
				$debugInfo = parent::startDebug();
				echo "<pre>" . var_dump($debugInfo) . "</pre>";
			} else parent::start();
		} catch (\Exception $e) {
			die(var_dump($e->getMessage()));
		}
	}

	private static function createRoutes(): void
	{
		$routesJson = json_decode(file_get_contents(LOCATION_BACKEND . "/config/routes.json"), true);

		foreach ($routesJson as $method => $routes) {
			foreach ($routes as $route) {
				$path = $route['path'] ?? ROUTER_DEFAULT_PREFIX;
				$controller = $route['controller'] ?? ROUTER_DEFAULT_CONTROLLER;
				$function = $route['function'] ?? ROUTER_DEFAULT_FUNCTION;
				$auth = $route['auth'] ?? true;
				$stopRedirect = $route['stopRedirect'] ?? false;

				self::registerRoute($method, $path, $controller, $function, $auth, $stopRedirect);
			}
		}
	}

	private static function registerRoute($method, $path = ROUTER_DEFAULT_PREFIX, $controller = ROUTER_DEFAULT_CONTROLLER, $function = ROUTER_DEFAULT_FUNCTION, $auth = true, $stopRedirect = false)
	{
		$method = strtoupper($method);
		if (!$auth) Helpers::registerNoAuthRoute($method, $path);
		if ($stopRedirect) Helpers::registerStopRedirectionRoute($method, $path);

		switch ($method) {
			case "GET":
				SimpleRouter::get($path, "{$controller}@{$function}");
				break;
			case "POST":
				SimpleRouter::post($path, "{$controller}@{$function}");
				break;
			case "PATCH":
				SimpleRouter::patch($path, "{$controller}@{$function}");
				break;
			case "DELETE":
				SimpleRouter::delete($path, "{$controller}@{$function}");
				break;
			default:
				SimpleRouter::all($path, "{$controller}@{$function}");
				break;
		}
	}
}
