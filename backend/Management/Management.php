<?php

namespace Management;

use Ouzo\Utilities\Arrays;

class Management
{
	public function __construct()
	{
		$this->url = MANAGEMENT_URL . "/api";
		$this->userToken = MANAGEMENT_USER_TOKEN;
		$this->appToken = MANAGEMENT_APP_TOKEN;
	}

	public function getUserIdInformatId($informatId)
	{
		$headers = $this->getHeaders();

		try {
			$url = $this->url . "/search/User?criteria[0][field]=16&criteria[0][searchtype]=contains&criteria[0][value]=^" . $informatId . "$&forcedisplay[0]=2";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

			$json = curl_exec($ch);
			curl_close($ch);
		} catch (Exception $e) {
			echo "get user id error: $e->getMessage()";
		}

		$obj = json_decode($json, true);
		return $obj['data']['0']['2'];
	}

	public function addOrUpdateUser($login, $password, $name, $firstName, $UID, $language = "nl_BE", $active = true)
	{
		set_time_limit(0);
		$existingUser = $this->getUserIdInformatId($UID);
		$headers = $this->getHeaders();

		$data = [];
		Arrays::setNestedValue($data, ["input", "name"], $login);
		Arrays::setNestedValue($data, ["input", "password"], sha1($password));
		Arrays::setNestedValue($data, ["input", "realname"], $name);
		Arrays::setNestedValue($data, ["input", "firstname"], $firstName);
		Arrays::setNestedValue($data, ["input", "language"], $language);
		Arrays::setNestedValue($data, ["input", "is_active"], (int)$active);
		Arrays::setNestedValue($data, ["input", "comment"], $UID);

		$data = json_encode($data);
		$url = $this->url . "/User";
		if (!is_null($existingUser)) $url .= "/" . $existingUser;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, (is_null($existingUser) ? 'POST' : 'PUT'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$json = curl_exec($ch);
		curl_close($ch);

		$res = json_decode($json, true);

		if ($res[0] == "ERROR_GLPI_ADD") {
			throw new \Exception($res[1]);
		}

		if (Arrays::contains([
			is_null($existingUser) ? $this->addUserEmail($UID, $login) : true
		], false)) return false;
		return true;
	}

	public function addUserEmail($UID, $email, $default = true)
	{
		$existingUser = $this->getUserIdInformatId($UID);
		$headers = $this->getHeaders();

		$data = [];
		Arrays::setNestedValue($data, ["input", "users_id"], $existingUser);
		Arrays::setNestedValue($data, ["input", "is_default"], (int)$default);
		Arrays::setNestedValue($data, ["input", "is_dynamic"], 0);
		Arrays::setNestedValue($data, ["input", "email"], $email);

		$data = json_encode($data);
		$url = $this->url . "/User/" . $existingUser . "/UserEmail/";

		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

			$json = curl_exec($ch);
			curl_close($ch);

			$res = json_decode($json, true);
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}

	private function getHeaders()
	{
		try {
			$api_url = $this->url;
			$user_token = $this->userToken;
			$app_token = $this->appToken;

			$url = $api_url . "/initSession?Content-Type=%20application/json&app_token=" . $app_token . "&user_token=" . $user_token;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$json = curl_exec($ch);

			curl_close($ch);

			$obj = json_decode($json, true);

			$sess_token = $obj['session_token'];
			$headers = array(
				'Content-Type: application/json',
				'App-Token: ' . $app_token,
				'Session-Token: ' . $sess_token
			);

			return $headers;
		} catch (\Exception $e) {
			echo "session opening error: $e->getMessage()";
		}
	}

	private function stop()
	{
	}
}
