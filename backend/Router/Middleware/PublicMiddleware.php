<?php

namespace Router\Middleware;

use Router\Helpers;
use Pecee\Http\Request;
use Database\Repository\Setting\Setting;
use Pecee\Http\Middleware\IMiddleware;
use Security\Code;

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
            $errorlocation = (new Setting)->getById("page.default.error")->value;
            Helpers::redirect("{$errorlocation}501", 501);
        }
    }

    static private function checkFileExistance()
    {
        $folder = Helpers::getDirectory();
        $errorlocation = (new Setting)->getById("page.default.error")->value;
        if (!file_exists(LOCATION_FRONTEND_PAGES . $folder)) Helpers::redirect("{$errorlocation}404", 404);
    }
}
