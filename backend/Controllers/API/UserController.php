<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Security\Input;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Module;
use O365\AuthenticationManager;
use Database\Repository\Setting;
use Database\Repository\LocalUser;
use Database\Repository\UserAddress;
use Database\Repository\UserProfile;
use Database\Repository\ModuleSetting;
use Database\Object\UserProfile as ObjectUserProfile;
use Database\Repository\Log;

class UserController extends ApiController
{
	// Login
	public function login()
	{
		$username = $password = null;

		$username = Helpers::input()->post("username")->getValue();
		$password = Helpers::input()->post("password")->getValue();

		if (!Input::check($username) || Input::empty($username))
			$this->setValidation('username', 'Gebruikersnaam moet ingevuld zijn!', self::VALIDATION_STATE_INVALID);
		else $this->setValidation('username');


		if (!Input::isEmail($username) && (!Input::check($password) || Input::empty($password)))
			$this->setValidation('password', 'Wachtwoord moet ingevuld zijn!', self::VALIDATION_STATE_INVALID);
		else $this->setValidation('password');

		if ($this->validationIsAllGood()) {
			if (Input::check($username, Input::INPUT_TYPE_EMAIL)) {
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
						Session::set(SECURITY_SESSION_ISSIGNEDIN, [
							'method' => SECURITY_SESSION_SIGNINMETHOD_LOCAL,
							'id' => $correctUser->id
						]);

						Log::write(userId: $correctUser->id, description: "User '{$correctUser->fullName}' logged in via local credentials!");

						$this->setRedirect(Helpers::url()->getScheme() . "://" . Helpers::url()->getHost() . Helpers::url((new Setting)->get(id: "page.default.afterLogin")[0]->value));
					}
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		$this->handle();
	}

	static public function apiLogin()
	{
		$username = $password = null;

		$authentication = Helpers::request()->getHeaders()['http_authentication'] ?? false;

		if ($authentication) {
			$authentication = explode(":", base64_decode(str_replace("Basic ", "", $authentication)));
			$username = $authentication[0];
			$password = $authentication[1];
		} else {
			$username = Helpers::request()->getHeaders()['php_auth_user'];
			$password = Helpers::request()->getHeaders()['php_auth_pw'];
		}

		$localUsers = (new LocalUser)->getByUsername($username);

		if (count($localUsers) == 0) echo "Gebruiker niet gevonden!";
		else {
			$correctUser = null;
			foreach ($localUsers as $localUser) {
				if (password_verify($password, $localUser->password)) {
					$correctUser = $localUser;
					break;
				}
			}

			if (!$correctUser->api) return false;

			Log::write(userId: $correctUser->id, description: "User '{$correctUser->fullName}' logged in via api credentials!");
			return true;
		}
	}

	public function O365Callback()
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

				$correctUser = (new LocalUser)->getByO365Id(Session::get('oid'));
				Log::write(userId: $correctUser->id, description: "User '{$correctUser->fullName}' logged in via Office 365!");

				header('Location: ' . (new Setting)->get(id: "page.default.afterLogin")[0]->value);
				exit();
			} catch (\RuntimeException $e) {
				echo 'Something went wrong, couldn\'t get tokens: ' . $e->getMessage();
			}
		}
	}

	// GET
	public function getProfile($view)
	{
		$userProfile = (new UserProfile)->getByUserId(User::getLoggedInUser()->id);
		$userProfile->link();
		if ($view == "form") $this->appendToJson(["fields"], $userProfile);

		$this->handle();
	}

	public function getAddress($view)
	{
		$userAddress = (new UserAddress)->getByUserId(User::getLoggedInUser()->id);

		if ($view == "table") {
			$this->appendToJson(
				key: 'columns',
				data: [
					[
						"type" => "icon",
						"title" => "Huidig",
						"data" => "currentIcon",
						"class" => ["w-1"]
					],
					[
						"title" => "Straat",
						"data" => "street",
						"width" => 300
					],
					[
						"title" => "Huisnummer",
						"data" => "number",
						"width" => 100
					],
					[
						"title" => "Bus",
						"data" => "bus",
						"width" => 100
					],
					[
						"title" => "Postcode",
						"data" => "zipcode",
						"width" => 100
					],
					[
						"title" => "Stad/Gemeente",
						"data" => "city",
						"width" => 300
					],
					[
						"title" => "Land",
						"data" => "country"
					]
				]
			);

			$this->appendToJson(["rows"], array_values($userAddress));
		} else if ($view == "select") $this->appendToJson(['items'], $userAddress);

		$this->handle();
	}

	public function getUsers($view, $part = null)
	{
		$users = (new LocalUser)->get();

		if ($part == "assignable") {
			$assignToIds = explode(";", (new ModuleSetting)->getByModuleAndKey((new Module)->getByModule("helpdesk")->id, "assignToIds")->value);
			$users = Arrays::filter($users, fn ($lu) => Strings::isNotBlank($lu->fullName));
			$users = Arrays::filter($users, fn ($lu) => Arrays::contains($assignToIds, $lu->id));
		} else if ($part == "acceptors") {
			$assignToIds = explode(";", (new ModuleSetting)->getByModuleAndKey((new Module)->getByModule("orders")->id, "acceptorIds")->value);
			$users = Arrays::filter($users, fn ($lu) => Strings::isNotBlank($lu->fullName));
			$users = Arrays::filter($users, fn ($lu) => Arrays::contains($assignToIds, $lu->id));
		}

		$this->appendToJson("items", Arrays::orderBy($users, "fullName"));
		if ($view == "select") $this->appendToJson(['items'], $users);

		$this->handle();
	}
}
