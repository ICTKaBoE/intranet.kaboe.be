<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Security\Input;
use Security\Session;
use O365\Repository\Group;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\LocalUser as ObjectLocalUser;
use Database\Object\UserProfile as ObjectUserProfile;
use Database\Object\UserSecurity as ObjectUserSecurity;
use Database\Repository\Module;
use O365\AuthenticationManager;
use Database\Repository\Setting;
use Database\Repository\LocalUser;
use Database\Repository\UserProfile;
use Database\Repository\UserSecurity;

class UserController extends ApiController
{
	public function login($prefix, $apiLogin = false)
	{
		$username = $password = null;

		if ($apiLogin) {
			$authentication = Helpers::request()->getHeaders()['http_authentication'] ?? false;

			if ($authentication) {
				$authentication = explode(":", base64_decode(str_replace("Basic ", "", $authentication)));
				$username = $authentication[0];
				$password = $authentication[1];
			} else {
				$username = Helpers::request()->getHeaders()['php_auth_user'];
				$password = Helpers::request()->getHeaders()['php_auth_pw'];
			}
		} else {
			$username = Helpers::input()->post("username")->getValue();
			$password = Helpers::input()->post("password")->getValue();
		}

		if (!$apiLogin) {
			if (!Input::check($username) || Input::empty($username))
				$this->setValidation('username', 'Gebruikersnaam moet ingevuld zijn!', self::VALIDATION_STATE_INVALID);

			if (!Input::isEmail($username) && (!Input::check($password) || Input::empty($password)))
				$this->setValidation('password', 'Wachtwoord moet ingevuld zijn!', self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			if (!$apiLogin && Input::check($username, Input::INPUT_TYPE_EMAIL)) {
				$this->setRedirect(AuthenticationManager::connect($username, false));
			} else {
				$localUsers = (new LocalUser)->getByUsername($username);

				if (count($localUsers) == 0) $this->setValidation('username', "Gebruiker niet gevonden!", self::VALIDATION_STATE_INVALID);
				else {
					$correctUser = null;
					foreach ($localUsers as $localUser) {
						if (password_verify($password, $localUser->password)) {
							$correctUser = $localUser;
							break;
						}
					}

					if (is_null($correctUser)) $this->setValidation('password', 'Wachtwoord is niet correct!', self::VALIDATION_STATE_INVALID);
					else if ($correctUser->active == 0) $this->setValidation("username", "Gebruiker kan niet aanmelden!", self::VALIDATION_STATE_INVALID);
					else {
						if ($apiLogin && !$correctUser->api) return false;
						Session::set(SECURITY_SESSION_ISSIGNEDIN, [
							'method' => SECURITY_SESSION_SIGNINMETHOD_LOCAL,
							'id' => $correctUser->id
						]);

						if (!$apiLogin) $this->setRedirect(Helpers::url()->getScheme() . "://" . Helpers::url()->getHost() . Helpers::url((new Setting)->get(id: "page.default.afterLogin")[0]->value));
						else return true;
					}
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		$this->handle();
	}

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
					'method' => 'o365',
					'id' => Session::get("oid")
				]);

				header('Location: ' . (new Setting)->get(id: "page.default.afterLogin")[0]->value);
				exit();
			} catch (\RuntimeException $e) {
				echo 'Something went wrong, couldn\'t get tokens: ' . $e->getMessage();
			}
		}
	}

	public function profile()
	{
		$mainSchoolId = Helpers::input()->post("mainSchoolId")->getValue();
		$bankAccount = Helpers::input()->post("bankAccount")->getValue();

		if (!Input::check($mainSchoolId, Input::INPUT_TYPE_INT) || Input::empty($mainSchoolId))
			$this->setValidation('mainSchoolId', 'Hoofdschool moet ingevuld zijn!', self::VALIDATION_STATE_INVALID);

		if (!Input::check($bankAccount) || Input::empty($bankAccount))
			$this->setValidation('bankAccount', 'Rekeningnummer moet ingevuld zijn!', self::VALIDATION_STATE_INVALID);

		if ($this->validationIsAllGood()) {
			$userProfileRepo = new UserProfile;
			$profile = $userProfileRepo->get(User::getLoggedInUser()->id)[0] ?? new ObjectUserProfile();
			$profile->userId = User::getLoggedInUser()->id;
			$profile->mainSchoolId = $mainSchoolId;
			$profile->bankAccount = $bankAccount;

			$userProfileRepo->set($profile);
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		$this->handle();
	}
}
