<?php

namespace Security;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\LocalUser;
use Database\Repository\UserSecurity;

abstract class User
{
    static public function isSignedIn()
    {
        return !is_null(Session::get(SECURITY_SESSION_ISSIGNEDIN));
    }

    static public function signInMethod()
    {
        return Session::get(SECURITY_SESSION_ISSIGNEDIN)['method'];
    }

    static public function signInId()
    {
        return Session::get(SECURITY_SESSION_ISSIGNEDIN)['id'];
    }

    static public function getLoggedInUser()
    {
        $method = self::signInMethod();
        $id = self::signInId();

        if (Strings::equal($method, SECURITY_SESSION_SIGNINMETHOD_LOCAL)) return (new LocalUser)->get($id)[0];
        else if (Strings::equal($method, SECURITY_SESSION_SIGNINMETHOD_O365)) return (new LocalUser)->getByO365Id($id);

        return false;
    }

    static public function hasPermissionToEnter($moduleId, $userId)
    {
        $repo = new UserSecurity;
        $items = $repo->getByUserAndModule($userId, 0);
        if ($items->view) return true;

        $items = $repo->getByUserAndModule($userId, $moduleId);
        if (!is_null($items) && $items->view) return true;

        return false;
    }

    static public function hasPermissionToEnterSub($navItem, $moduleId, $userId)
    {
        $repo = new UserSecurity;
        $items = $repo->getByUserAndModule($userId, 0);

        if ($items->view) return true;
        else {
            $permissions = $repo->getByUserAndModule($userId, $moduleId);

            if (is_null($permissions)) return false;
            else {
                $hasPermission = false;

                foreach (UserSecurity::$rightsOrder as $index => $value) {
                    if (Strings::equalsIgnoreCase($navItem->minimumRights, $value)) {
                        if ($permissions->$value) {
                            $hasPermission = true;
                            break;
                        }
                    }
                }

                return $hasPermission;
            }
        }
    }
}
