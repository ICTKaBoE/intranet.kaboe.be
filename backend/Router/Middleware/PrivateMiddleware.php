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

class PrivateMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        self::checkDatabaseVersion();
        self::checkUserCanAccess();
        self::checkFileExistance();
    }

    static private function checkDatabaseVersion()
    {
        if (!Code::CheckDatabaseVersion()) {
            $errorlocation = (new Setting)->getById("page.default.error")->value;
            Helpers::redirect("{$errorlocation}501", 501);
        }
    }

    static private function checkUserCanAccess()
    {
        $directory = "/" . Helpers::getDomainFolder();
        $folder = Helpers::getDirectory();
        $defaultPage = (new Setting)->getById((User::isSignedIn() ? "page.default.afterLogin" : "page.default.login"))->value;

        if (User::isSignedIn()) {
            if (Strings::isBlank(str_replace($directory, "", $folder)) && !Strings::startsWith($folder, "{$directory}/error") && !Strings::equal($folder, $defaultPage)) Helpers::redirect($defaultPage);
        } else {
            $redirect = Helpers::url()->getParam("redirect") ?? Helpers::url()->getScheme() . "://" . Helpers::url()->getHost() . Helpers::url()->getPath();
            if (!Strings::equal($folder, $defaultPage)) Helpers::redirect($defaultPage . ($redirect ? "?redirect={$redirect}" : ""));
        }
    }

    static private function checkFileExistance()
    {
        $folder = Helpers::getDirectory();
        $errorlocation = (new Setting)->getById("page.default.error")->value;
        if (!file_exists(LOCATION_FRONTEND_PAGES . $folder)) Helpers::redirect("{$errorlocation}404", 404);
    }
}
