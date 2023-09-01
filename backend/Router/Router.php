<?php

namespace Router;

use Ouzo\Utilities\Arrays;
use Pecee\SimpleRouter\SimpleRouter;

class Router extends SimpleRouter
{
	public static function start($debug = false): void
	{
		self::createRoutes();
		// parent::setDefaultNamespace(ROUTER_DEFAULT_NAMESPACE);

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
		// Default Middleware
		SimpleRouter::group(['middleware' => ROUTER_DEFAULT_MIDDLEWARE], function () {

			// Default route
			SimpleRouter::group(['prefix' => "/"], function () {
			});

			// Error Group
			SimpleRouter::group(['prefix' => '/error'], function () {
				// Code route
				self::registerRoute('GET', "/{code}", \Controllers\ERROR\ErrorController::class);
			});

			// Public
			SimpleRouter::group(['prefix' => '/public'], function () {
				self::registerRoute('GET', "/{module?}/{page?}/{id?}");
			});

			// App
			SimpleRouter::group(['prefix' => '/app'], function () {
				self::registerRoute('GET', "/{module?}/{page?}/{method?}/{id?}");
			});
		});

		// API Middleware - API
		// SimpleRouter::group(['prefix' => '/api/v1.0', 'middleware' => \Router\Middleware\ApiMiddleware::class], function () {
		// 	SimpleRouter::group(['prefix' => "/cron"], function () {
		// 		// GET
		// 		self::registerRoute('GET', '/user/o365/callback', \Controllers\API\UserController::class, 'O365Callback');

		// 		// POST
		// 		self::registerRoute('POST', '/user/login', \Controllers\API\UserController::class, 'login', true);
		// 	});
		// });
	}

	private static function registerRoute($method, $path = ROUTER_DEFAULT_PREFIX, $controller = ROUTER_DEFAULT_CONTROLLER, $function = ROUTER_DEFAULT_FUNCTION, $noAuth = false)
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
