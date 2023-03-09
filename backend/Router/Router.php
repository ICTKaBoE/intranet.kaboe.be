<?php

namespace Router;

use Ouzo\Utilities\Arrays;
use Pecee\SimpleRouter\SimpleRouter;

class Router extends SimpleRouter
{
	public static function start($debug = false): void
	{
		self::createRoutes();
		parent::setDefaultNamespace(ROUTER_DEFAULT_NAMESPACE);

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
		$json = json_decode(file_get_contents(__DIR__ . "/../config/routes.json"), true);

		foreach ($json as $group) {
			$prefix = Arrays::getValue($group, 'prefix', ROUTER_DEFAULT_PREFIX);
			$middleware = Arrays::getValue($group, 'middleware', ROUTER_DEFAULT_MIDDLEWARE);

			SimpleRouter::group(['prefix' => $prefix, 'middleware' => $middleware], function () use ($group) {
				foreach ($group['methods'] as $method => $routes) {
					foreach ($routes as $route) {
						$path = is_string($route) ? $route : Arrays::getValue($route, 'path', "");
						$controller = is_string($route) ? ROUTER_DEFAULT_CONTROLLER : Arrays::getValue($route, 'controller', ROUTER_DEFAULT_CONTROLLER);
						$function = is_string($route) ? ROUTER_DEFAULT_FUNCTION : Arrays::getValue($route, 'function', ROUTER_DEFAULT_FUNCTION);

						if (is_array($path)) {
							foreach ($path as $p) self::registerRoute($method, $p, $controller, $function);
						} else self::registerRoute($method, $path, $controller, $function);
					}
				}
			});
		}
	}

	private static function registerRoute($method, $path, $controller, $function)
	{
		switch (strtoupper($method)) {
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
