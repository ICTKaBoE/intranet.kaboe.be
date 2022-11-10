<?php

use Router\Helpers;
use Security\Input;
use Security\Session;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Setting;
use Database\Repository\LocalUser;

include_once "../../autoload.php";

$return = [];
$continue = true;

$email = $_POST['email'];
$password = $_POST['password'];

if (Strings::isBlank($email)) {
	$continue = false;
	Arrays::setNestedValue($return, ['validation', 'email', 'state'], 'invalid');
	Arrays::setNestedValue($return, ['validation', 'email', 'feedback'], "Email is verplicht!");
}

if (!Strings::contains($email, "@") && Strings::isBlank($password)) {
	$continue = false;
	Arrays::setNestedValue($return, ['validation', 'password', 'state'], 'invalid');
	Arrays::setNestedValue($return, ['validation', 'password', 'feedback'], "Wachtwoord is verplicht!");
}

if ($continue) {
	if (Input::check($email, Input::INPUT_TYPE_EMAIL)) {
		Arrays::setNestedValue($return, ['redirect'], O365\AuthenticationManager::connect($email, false));
	} else {
		$localUsers = (new LocalUser)->getByUsername($email);

		if (count($localUsers) == 0) {
			Arrays::setNestedValue($return, ['validation', 'email', 'state'], 'invalid');
			Arrays::setNestedValue($return, ['validation', 'email', 'feedback'], "Gebruiker niet gevonden!");
		} else {
			$correctUser = null;
			foreach ($localUsers as $localUser) {
				if (password_verify($password, $localUser->password)) {
					$correctUser = $localUser;
					break;
				}
			}

			if (is_null($correctUser)) {
				Arrays::setNestedValue($return, ['validation', 'password', 'state'], 'invalid');
				Arrays::setNestedValue($return, ['validation', 'password', 'feedback'], "Wachtwoord is niet correct!");
			} else {
				Session::set(SECURITY_SESSION_ISSIGNEDIN, [
					'method' => SECURITY_SESSION_SIGNINMETHOD_LOCAL,
					'id' => $correctUser->id
				]);

				Arrays::setNestedValue($return, ['redirect'], Helpers::url()->getScheme() . "://" . Helpers::url()->getHost() . Helpers::url((new Setting)->get(id: "page.default.afterLogin")[0]->value));
			}
		}
	}
}

echo Json::safeEncode($return);
