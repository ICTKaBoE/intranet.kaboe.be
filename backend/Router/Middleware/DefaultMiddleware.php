<?php

namespace Router\Middleware;

use Router\Helpers;
use Pecee\Http\Request;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Pecee\Http\Middleware\IMiddleware;
use Security\User;

class DefaultMiddleware implements IMiddleware
{
    public function handle(Request $request): void
    {
        self::checkUserCanAccess();
        self::checkFileExistance();
    }

    private function checkUserCanAccess()
    {
        // $folder = Helpers::getReletiveUrl();
        $folder = Helpers::getDirectory();
        $defaultPage = (new Setting)->get(id: (User::isSignedIn() ? "page.default.afterLogin" : "page.default.public"))[0]->value;

        if (User::isSignedIn()) {
            if (Strings::isBlank($folder) && !Strings::startsWith($folder, "/error") && !Strings::equal($folder, $defaultPage)) Helpers::redirect($defaultPage);
        } else {
            if (!Strings::equal($folder, $defaultPage)) Helpers::redirect($defaultPage);
        }
    }

    static private function checkFileExistance()
    {
        $folder = Helpers::getDirectory();
        if (!file_exists(LOCATION_FRONTEND_PAGES . $folder)) Helpers::redirect("/error/404", 404);
    }
}
