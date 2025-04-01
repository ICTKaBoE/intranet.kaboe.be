<?php

namespace JAMF\Interface;

use Database\Repository\Setting\Setting;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class Repository
{
	public function __construct($endpoint, $object)
	{
		$settingRepo = new Setting;
		$this->version = Arrays::first($settingRepo->get("jamf.version"))->value;
		$this->networkid = Arrays::first($settingRepo->get("jamf.networkid"))->value;
		$this->key = Arrays::first($settingRepo->get("jamf.key"))->value;
		$this->username = Arrays::first($settingRepo->get("jamf.username"))->value;
		$this->password = Arrays::first($settingRepo->get("jamf.password"))->value;
		$this->_endpoint = $endpoint;

		$this->endpoint = Path::normalize(Arrays::first($settingRepo->get("jamf.endpoint"))->value . "/{$this->version}" . "/{$this->_endpoint}");
		$this->auth = base64_encode("{$this->networkid}:{$this->key}");

		$this->object = $object;
	}

	private function createConnection($method = 'GET')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Basic {$this->auth}",
			"Content-Type: application/x-www-form-urlencoded; charset=utf-8",
		]);

		$this->connection = $ch;
	}

	private function createBody($body)
	{
		$method = curl_getinfo($this->connection, CURLOPT_CUSTOMREQUEST);
	}

	private function executeRequest()
	{
		$resp = curl_exec($this->connection);

		if (!$resp) {
			die('Error: "' . curl_error($this->connection) . '" - Code: ' . curl_errno($this->connection));
		} else {
			return $resp;
		}
	}

	private function closeConnection()
	{
		curl_close($this->connection);
		$this->connection = null;
	}

	public function get()
	{
		$this->createConnection();
		$result = $this->executeRequest();
		$this->closeConnection();

		$result = json_decode($result, true);
		return $this->convertRowsToObject($result[$this->_endpoint]);
	}

	protected function convertRowsToObject($rows)
	{
		$objects = [];
		foreach ($rows as $row) {
			try {
				if (!empty($row)) $objects[] = $this->convertRowToObject($row);
			} catch (\Exception $e) {
				die(var_dump($e->getMessage()));
			}
		}
		return $objects;
	}

	protected function convertRowToObject($row)
	{
		foreach ($row as $key => $value) {
			if (is_array($value) && empty($value)) $row[$key] = null;
		}

		return new $this->object($row);
	}
}
