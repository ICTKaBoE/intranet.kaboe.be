<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Security\Session;
use Database\Repository\User;
use Controllers\ApiController;
use Database\Repository\Setting;
use Security\User as SecurityUser;
use Database\Repository\UserAddress;
use Database\Repository\UserLoginHistory;
use Database\Object\UserLoginHistory as ObjectUserLoginHistory;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class UserController extends ApiController
{
    public function get($view, $what = null, $id = null)
    {
        if (Strings::equal($what, "address")) $this->getAddress($view, $id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function post($what, $id = null)
    {
        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function login()
    {
        $username = Helpers::input()->post("username")->getValue();
        $password = Helpers::input()->post("password")->getValue();

        if (!Input::check($username) || Input::empty($username)) $this->setValidation('username', "Gebruikersnaam kan niet leeg zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::isEmail($username) && (!Input::check($password) || Input::empty($password))) $this->setValidation('password', 'Wachtwoord kan niet leeg zijn!', self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            if (Input::isEmail($username) && Input::check($username, Input::INPUT_TYPE_EMAIL)) {
                $this->setValidation("username", "Gelieve aan te melden via de knop 'Aanmelden via Office 365'!");
            } else {
                $users = (new User)->getByUsername($username);

                if (!count($users)) $this->setValidation('username', 'Gebruikersnaam niet gevonden!', self::VALIDATION_STATE_INVALID);
                else {
                    $loginUser = null;

                    foreach ($users as $user) {
                        if (password_verify($password, $user->password)) {
                            $loginUser = $user;
                            break;
                        }
                    }

                    if (is_null($loginUser)) $this->setValidation('password', 'Wachtwoord komt niet overeen!', self::VALIDATION_STATE_INVALID);
                    else if (!$loginUser->active) $this->setValidation('username', 'Gebruiker is niet toegestaan aan te melden!');
                    else {
                        Session::set(SECURITY_SESSION_ISSIGNEDIN, [
                            'method' => SECURITY_SESSION_SIGNINMETHOD_LOCAL,
                            'id' => $loginUser->id
                        ]);

                        $userLoginHistory = new ObjectUserLoginHistory([
                            "userId" => $loginUser->id,
                            "source" => SECURITY_SESSION_SIGNINMETHOD_LOCAL
                        ]);

                        (new UserLoginHistory)->set($userLoginHistory);

                        $this->setRedirect(Helpers::url()->getScheme() . "://" . Helpers::url()->getHost() . (new Setting)->get(id: "page.default.afterLogin")[0]->value);
                    }
                }
            }
        }

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    // Get Functions
    private function getAddress($view, $id)
    {
        $repo = new UserAddress;
        $currentUserId = SecurityUser::getLoggedInUser()->id;

        if (Strings::equal($view, "table")) {
        } else if (Strings::equal($view, "select")) {
            $address = $repo->getByUserId($currentUserId);
            Arrays::each($address, fn($a) => $a->address = $a->formatted->address);
            $this->appendToJson('items', $address);
        } else if (Strings::equal($view, "form")) {
        }
    }

    // Post Functions
}
