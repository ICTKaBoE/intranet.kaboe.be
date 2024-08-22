<?php

namespace Router;

use Database\Repository\Route;
use Database\Repository\RouteGroup;
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

    private static function createRoutes()
    {
        $routeRepo = new Route;
        $routeGroups = (new RouteGroup)->get();

        foreach ($routeGroups as $rg) {
            SimpleRouter::group(['prefix' => $rg->prefix, 'controller' => $rg->controller, 'middleware' => $rg->middleware], function () use ($routeRepo, $rg) {
                $routes = $routeRepo->getByRouteGroupId($rg->id);

                foreach ($routes as $r) {
                    switch ($r->method) {
                        case "GET":
                            SimpleRouter::get($r->route, [$r->controller ?: $rg->controller, $r->callback]);
                            break;
                        case "POST":
                            SimpleRouter::post($r->route, [$r->controller ?: $rg->controller, $r->callback]);
                            break;
                        case "DELETE":
                            SimpleRouter::delete($r->route, [$r->controller ?: $rg->controller, $r->callback]);
                            break;
                        case "PATCH":
                            SimpleRouter::patch($r->route, [$r->controller ?: $rg->controller, $r->callback]);
                            break;
                        case "PUT":
                            SimpleRouter::put($r->route, [$r->controller ?: $rg->controller, $r->callback]);
                            break;
                        default:
                            SimpleRouter::all($r->route, [$r->controller ?: $rg->controller, $r->callback]);
                    }
                }
            });
        }
    }
}
