<?php

namespace Router\Middleware;

use Router\Helpers;
use Pecee\Http\Request;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting\Setting;
use Ouzo\Utilities\Arrays;
use Pecee\Http\Middleware\IMiddleware;
use Security\Code;
use Security\User;

class PublicMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        if (!Helpers::isErrorPage()) {
            self::checkDatabaseVersion();
        }
        self::checkFileExistance();
    }

    static private function checkDatabaseVersion()
    {
        if (!Code::CheckDatabaseVersion()) {
            $errorlocation = Arrays::first((new Setting)->get(id: "page.default.error"))->value;
            Helpers::redirect("{$errorlocation}501", 501);
        }
    }

    static private function checkFileExistance()
    {
        $folder = Helpers::getDirectory();
        $errorlocation = Arrays::first((new Setting)->get(id: "page.default.error"))->value;
        if (!file_exists(LOCATION_FRONTEND_PAGES . $folder)) Helpers::redirect("{$errorlocation}404", 404);
    }
}
