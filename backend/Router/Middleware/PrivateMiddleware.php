<?php

namespace Router\Middleware;

use Router\Helpers;
use Pecee\Http\Request;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Ouzo\Utilities\Arrays;
use Pecee\Http\Middleware\IMiddleware;
use Security\User;

class PrivateMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        self::checkUserCanAccess();
        self::checkFileExistance();
    }

    private function checkUserCanAccess()
    {
        $directory = "/" . Helpers::getDomainFolder();
        $folder = Helpers::getDirectory();
        $defaultPage = Arrays::first((new Setting)->get((User::isSignedIn() ? "page.default.afterLogin" : "page.default.login")))->value;

        if (User::isSignedIn()) {
            if (Strings::isBlank(str_replace($directory, "", $folder)) && !Strings::startsWith($folder, "{$directory}/error") && !Strings::equal($folder, $defaultPage)) Helpers::redirect($defaultPage);
        } else {
            $redirect = Helpers::url()->getAbsoluteUrl();
            if (!Strings::equal($folder, $defaultPage)) Helpers::redirect($defaultPage . ($redirect ? "?redirect={$redirect}" : ""));
        }
    }

    static private function checkFileExistance()
    {
        $folder = Helpers::getDirectory();
        $errorlocation = Arrays::first((new Setting)->get(id: "page.default.error"))->value;
        if (!file_exists(LOCATION_FRONTEND_PAGES . $folder)) Helpers::redirect("{$errorlocation}404", 404);
    }
}
