<?php

namespace Controllers;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;

class ApiController
{
	const VALIDATION_STATE_VALID = "valid";
	const VALIDATION_STATE_INVALID = "invalid";

	private $httpCode = 200;
	private $validation = [];
	private $reload = false;

	private $json = [];

	public function handle()
	{
		if ($this->httpCode) Helpers::response()->httpCode($this->httpCode);

		if ($this->validation) Arrays::setNestedValue($this->json, ['validation'], $this->validation);
		if ($this->redirect) Arrays::setNestedValue($this->json, ['redirect'], $this->redirect);
		if ($this->reload) Arrays::setNestedValue($this->json, ['reload'], $this->reload);

		Helpers::response()->json($this->json);
	}

	protected function setHttpCode($code)
	{
		$this->httpCode = $code;
	}

	protected function setValidation($input, $feedback = null, $state = self::VALIDATION_STATE_VALID)
	{
		Arrays::setNestedValue($this->validation, [$input, 'state'], $state);
		if (!is_null($feedback)) Arrays::setNestedValue($this->validation, [$input, 'feedback'], $feedback);
	}

	protected function removeValidation($input)
	{
		Arrays::removeNestedKey($this->validation, [$input]);
	}

	protected function setRedirect($url)
	{
		$this->redirect = $url;
	}

	protected function setReload()
	{
		$this->reload = true;
	}

	protected function appendToJson($key = [], $data = false)
	{
		if ($data) {
			if (!is_null($key) && !empty($key)) {
				if (is_string($key)) $key = [$key];
				Arrays::setNestedValue($this->json, $key, $data);
			} else $this->json[] = $data;
		}
	}

	protected function validationIsAllGood()
	{
		return count($this->validation) == Arrays::count($this->validation, fn ($v) => Strings::equal($v['state'], self::VALIDATION_STATE_VALID));
	}

	protected function getValidation()
	{
		return $this->validation;
	}
}
