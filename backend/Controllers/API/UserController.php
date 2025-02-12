<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\User;
use Controllers\ApiController;
use Database\Repository\Setting;
use Security\User as SecurityUser;
use Database\Repository\UserAddress;
use Database\Repository\UserLoginHistory;
use Database\Object\UserLoginHistory as ObjectUserLoginHistory;

class UserController extends ApiController
{
    public function login()
    {
        $username = Helpers::input()->post("username")->getValue();
        $password = Helpers::input()->post("password")->getValue();
        $redirect = Helpers::url()->getParam("redirect");

        if (!Input::check($username) || Input::empty($username)) $this->setValidation('username', state: self::VALIDATION_STATE_INVALID);
        if (!Input::isEmail($username) && (!Input::check($password) || Input::empty($password))) $this->setValidation('password', state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            if (Input::isEmail($username) && Input::check($username, Input::INPUT_TYPE_EMAIL)) {
                $this->setValidation("username", state: self::VALIDATION_STATE_INVALID);
                $this->setToast("Gelieve aan te melden via de knop 'Aanmelden via Office 365'!", self::VALIDATION_STATE_INVALID);
            } else {
                $users = (new User)->getByUsername($username);

                if (!count($users)) {
                    $this->setValidation('username', state: self::VALIDATION_STATE_INVALID);
                    $this->setToast("Gebruikersnaam niet gevonden!", self::VALIDATION_STATE_INVALID);
                } else {
                    $loginUser = null;

                    foreach ($users as $user) {
                        if (password_verify($password, $user->password)) {
                            $loginUser = $user;
                            break;
                        }
                    }

                    if (is_null($loginUser)) {
                        $this->setValidation('password', state: self::VALIDATION_STATE_INVALID);
                        $this->setToast("Wachtwoord komt niet overeen!", self::VALIDATION_STATE_INVALID);
                    } else if (!$loginUser->active) {
                        $this->setValidation('username', state: self::VALIDATION_STATE_INVALID);
                        $this->setToast("Gebruiker is niet toegestaan aan te melden!", self::VALIDATION_STATE_INVALID);
                    } else {
                        Session::set(SECURITY_SESSION_ISSIGNEDIN, [
                            'method' => SECURITY_SESSION_SIGNINMETHOD_LOCAL,
                            'id' => $loginUser->id
                        ]);

                        $userLoginHistory = new ObjectUserLoginHistory([
                            "userId" => $loginUser->id,
                            "source" => SECURITY_SESSION_SIGNINMETHOD_LOCAL
                        ]);

                        (new UserLoginHistory)->set($userLoginHistory);

                        if ($redirect) $this->setRedirect($redirect);
                        else $this->setRedirect((new Setting)->get(id: "page.default.afterLogin")[0]->value);
                    }
                }
            }
        } else {
            $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
        }

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    static public function ApiLogin()
    {
        $authentication = getallheaders()["X-Authorization"];

        if ($authentication) {
            $authentication = explode(":", base64_decode(str_replace("Basic ", "", $authentication)));
            $username = $authentication[0];
            $password = $authentication[1];

            $users = (new User)->getByUsername($username);

            if (!count($users)) return null;
            else {
                $loginUser = null;

                foreach ($users as $user) {
                    if (password_verify($password, $user->password)) {
                        $loginUser = $user;
                        break;
                    }
                }

                if (is_null($loginUser)) return null;
                else if (!$loginUser->active) return null;
                else {
                    Session::set(SECURITY_SESSION_ISSIGNEDIN, [
                        'method' => SECURITY_SESSION_SIGNINMETHOD_LOCAL,
                        'id' => $loginUser->id
                    ]);

                    return $loginUser;
                }
            }
        } else return null;
    }

    // Get Functions
    protected function getList($view, $id)
    {
        $repo = new User;
        $loginRepo = new UserLoginHistory;
        $filters = [
            'id' => Arrays::filter(explode(";", Helpers::url()->getParam('id')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get();
            General::filter($items, $filters);
            $items = Arrays::map($items, fn($i) => $i = $i->toArray(true));
            $this->appendToJson('items', array_values($items));
        } else if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", false);
            $this->appendToJson("defaultOrder", [[0, "asc"], [1, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "Naam",
                        "data" => "name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Voornaam",
                        "data" => "firstName",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Gebruikersnaam",
                        "data" => "username"
                    ],
                    [
                        "title" => "Laatste aanmelding",
                        "data" => "formatted.lastLogin",
                        "width" => "250px"
                    ]
                ]
            );

            $items = $repo->get();
            Arrays::each($items, function ($i) use ($loginRepo) {
                $lastLogin = $loginRepo->getByUserId($i->id);
                $lastLogin = Arrays::firstOrNull($lastLogin);

                $i->formatted->lastLogin = $lastLogin ? Clock::at($lastLogin->timestamp)->plusHours(1)->format("d/m/Y H:i:s") . " (" . (Strings::equal($lastLogin->source, "local") ? "Lokaal" : "Office 365") . ")" : null;
            });
            $this->appendToJson("rows", array_values($items));
        }
    }

    protected function getAddress($view, $id)
    {
        $repo = new UserAddress;
        $currentUserId = SecurityUser::getLoggedInUser()->id;

        if (Strings::equal($view, self::VIEW_TABLE)) {
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $address = $repo->getByUserId($currentUserId);
            $address = Arrays::map($address, fn($a) => $a = $a->toArray(true));
            $this->appendToJson('items', $address);
        } else if (Strings::equal($view, self::VIEW_FORM)) {
        }
    }

    // Post Functions
}
