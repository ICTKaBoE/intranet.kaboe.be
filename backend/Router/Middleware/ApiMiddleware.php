<?php

namespace Router\Middleware;

use Controllers\API\UserController;
use Database\Repository\Route;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Router\Helpers;
use Security\User;

class ApiMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        $loadedRoute = Helpers::request()->getLoadedRoute()->getUrl();
        $route = Arrays::firstOrNull(Arrays::filter((new Route)->get(), fn($r) => Strings::equal($r->formatted->full, $loadedRoute)));

        if (!$route) Helpers::response()->httpCode(404)->json(["error" => "Route not found!"]);
        else {
            if (!$route->apiNoAuth) {
                $user = null;
                if (User::isSignedIn()) $user = User::getLoggedInUser();
                else $user = UserController::ApiLogin();

                if (!$user) Helpers::response()->httpCode(401)->json(["error" => "You are not authorized!"]);
                else if (!$user->api) Helpers::response()->httpCode(401)->json(["error" => "You are not able to use the API!"]);
            }
        }
    }
}
