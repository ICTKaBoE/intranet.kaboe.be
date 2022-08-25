<?php

namespace Core;

use Security\User;
use Security\Session;

abstract class Bootstrap
{
    static public function init()
    {
        global $isSignedIn, $pageExists;

        Session::remove(SECURITY_SESSION_PAGEERROR);
        $isSignedIn = User::isSignedIn();
        $pageExists = Page::exists();

        if (!$pageExists) http_response_code(404);
    }
}
