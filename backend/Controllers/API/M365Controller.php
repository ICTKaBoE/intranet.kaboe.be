<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Session;
use Controllers\ApiController;
use Database\Object\User\LoginHistory as ObjectUserLoginHistory;
use M365\AuthenticationManager;
use Database\Repository\Setting\Setting;
use Database\Repository\User\LoginHistory;
use Database\Repository\User\User;

class M365Controller extends ApiController
{
    public function callback()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
            Session::start();
            if (Helpers::input()->exists('admin_consent')) Session::set('admin_consent', Helpers::input()->get('admin_consent'));
            if (Helpers::input()->exists('code')) Session::set('code', Helpers::input()->get('code'));
            if (Helpers::input()->exists('session_state')) Session::set('session_state', Helpers::input()->get('session_state'));
            if (Helpers::input()->exists('state')) Session::set('state', Helpers::input()->get('state'));

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

                    header('Location: ' . (new Setting)->get(id: "page.default.afterLogin")[0]->value);
                    exit();
                }
            } catch (\RuntimeException $e) {
                echo 'Something went wrong, couldn\'t get tokens: ' . $e->getMessage();
            }
        }
    }
}
