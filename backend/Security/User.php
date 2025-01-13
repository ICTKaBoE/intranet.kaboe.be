<?php

namespace Security;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Module;
use Database\Repository\ModuleSetting;
use Database\Repository\SecurityGroup;
use Database\Repository\SecurityGroupUser;
use Database\Repository\Setting;
use Database\Repository\User as RepositoryUser;

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
        $userSecurityGroup = Arrays::firstOrNull((new SecurityGroupUser)->getByUserId($user->id));
        if (!$userSecurityGroup) return false;

        $securityGroup = Arrays::firstOrNull((new SecurityGroup)->get($userSecurityGroup->securityGroupId));
        if (!$securityGroup) return false;

        $firstIsTrue = Arrays::findKeyByValue($minimumRights, 1);

        return Strings::equal($securityGroup->permission[$firstIsTrue], 1);
    }

    static public function generatePassword()
    {
        $settingRepo = new Setting;
        $dictionary = $settingRepo->get("dictionary")[0]->value;
        $words = explode(PHP_EOL, $dictionary);

        $password = Arrays::randElement($words);
        $password .= str_pad(rand(0, pow(10, 2) - 1), 2, '0', STR_PAD_LEFT);

        return $password;
    }
}
