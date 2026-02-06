<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Session;
use Controllers\ApiController;
use M365\AuthenticationManager;
use Database\Repository\User\User;
use Database\Repository\Setting\Setting;
use Database\Repository\User\LoginHistory;
use Database\Object\User\LoginHistory as ObjectUserLoginHistory;

class M365Controller extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "m365";

    public function callback()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && Helpers::input()->exists('code')) {
            Session::start();
            if (Helpers::input()->exists('admin_consent')) Session::set('admin_consent', Helpers::input()->get('admin_consent')->getValue());
            if (Helpers::input()->exists('code')) Session::set('code', Helpers::input()->get('code')->getValue());
            if (Helpers::input()->exists('session_state')) Session::set('session_state', Helpers::input()->get('session_state')->getValue());
            if (Helpers::input()->exists('state')) Session::set('state', Helpers::input()->get('state')->getValue());

            // With the authorization code, we can retrieve access tokens and other data.
            try {
                AuthenticationManager::acquireToken();
                Session::set(SECURITY_SESSION_ISSIGNEDIN, [
                    'method' => SECURITY_SESSION_SIGNINMETHOD_M365,
                    'id' => Session::get("oid")
                ]);

                $loginUser = (new User)->getByEntraId(Session::get("oid"));

                if ($loginUser) {
                    $userLoginHistory = new ObjectUserLoginHistory([
                        "userId" => $loginUser->id,
                        "source" => SECURITY_SESSION_SIGNINMETHOD_M365
                    ]);

                    (new LoginHistory)->set($userLoginHistory);

                    header('Location: ' . (new Setting)->getById("page.default.afterLogin")->value);
                    exit();
                } else {
                    header('Location: ' . (new Setting)->getById("page.default.login")->value);
                    exit();
                }
            } catch (\RuntimeException $e) {
                echo 'Something went wrong, couldn\'t get tokens: ' . $e->getMessage();
            }
        }
    }
}
