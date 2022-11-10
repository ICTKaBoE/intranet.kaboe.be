<?php

namespace Router;

use Router\Middleware;
use Pecee\SimpleRouter\SimpleRouter;

class Router extends SimpleRouter
{
	public static function start($debug = false): void
	{
		self::createRoutes();
		parent::setDefaultNamespace("\Controllers");

		if ($debug) parent::startDebug();
		else parent::start();
	}

	private static function createRoutes(): void
	{
		$json = json_decode(file_get_contents("./app/config/routes.json"), true);

		SimpleRouter::group(['middleware' => Middleware::class], function () use ($json) {
			foreach ($json as $method => $routes) {
				foreach ($routes as $route) {
					$path = "";
					$controller = "Controller";
					$classMethod = "index";

					if (is_string($route)) $path = $route;
					else {
						$path = $route['path'];
						$controller = $route['controller'] ?? "Controller";
						$classMethod = $route['method'] ?? "index";
					}

					switch ($method) {
						case 'get':
							SimpleRouter::get($path, "{$controller}@{$classMethod}");
							break;
					}
				}
			}
		});
	}
}
