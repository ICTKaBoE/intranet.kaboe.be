<?php

namespace Security;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Module;
use Database\Repository\ModuleSetting;
use Database\Repository\Security\Group;
use Database\Repository\Security\GroupUser;
use Database\Repository\Setting\Setting;
use Database\Repository\User\User as RepositoryUser;

abstract class User
{
    static public function isSignedIn()
    {
        return !is_null(Session::get(SECURITY_SESSION_ISSIGNEDIN));
    }

    static public function signInMethod()
    {
        return Session::get(SECURITY_SESSION_ISSIGNEDIN)['method'] ?? null;
    }

    static public function signInId()
    {
        return Session::get(SECURITY_SESSION_ISSIGNEDIN)['id'] ?? null;
    }

    static public function getLoggedInUser()
    {
        $method = self::signInMethod();
        if (is_null($method)) return false;

        $id = self::signInId();

        if (Strings::equal($method, SECURITY_SESSION_SIGNINMETHOD_LOCAL)) return (new RepositoryUser)->get($id)[0];
        else if (Strings::equal($method, SECURITY_SESSION_SIGNINMETHOD_M365)) return (new RepositoryUser)->getByEntraId($id);

        return false;
    }

    static public function canAccess($minimumRights)
    {
        $user = self::getLoggedInUser();
        $userSecurityGroup = (new GroupUser)->getByUserId($user->id);
        if (!$userSecurityGroup) return false;

        $permissions = [];
        foreach ($userSecurityGroup as $usg) {
            $securityGroup = Arrays::firstOrNull((new Group)->get($usg->securityGroupId));
            if (!$securityGroup) continue;

            if (empty($permissions)) $permissions = $securityGroup->permission;
            else {
                foreach ($securityGroup->permission as $index => $value) {
                    if ($permissions[$index] == 0 && $value == 1) $permissions[$index] = 1;
                }
            }
        }

        $firstIsTrue = Arrays::findKeyByValue($minimumRights, 1);
        return Strings::equal($permissions[$firstIsTrue], 1);
    }

    static public function generatePassword()
    {
        $settingRepo = new Setting;
        $dictionary = $settingRepo->get("dictionary")[0]->value;
        $words = explode(PHP_EOL, $dictionary);

        $password = trim(Arrays::randElement($words));
        $password .= str_pad(rand(0, pow(10, 2) - 1), 2, '0', STR_PAD_LEFT);
        $password = str_replace(" ", "", $password);

        return $password;
    }
}
