<?php

namespace Router\Middleware;

use Router\Helpers;
use Pecee\Http\Request;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Ouzo\Utilities\Arrays;
use Pecee\Http\Middleware\IMiddleware;
use Security\User;

class PublicMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        self::checkFileExistance();
    }

    static private function checkFileExistance()
    {
        $folder = Helpers::getDirectory();
        $errorlocation = Arrays::first((new Setting)->get(id: "page.default.error"))->value;
        if (!file_exists(LOCATION_FRONTEND_PAGES . $folder)) Helpers::redirect("{$errorlocation}404", 404);
    }
}
