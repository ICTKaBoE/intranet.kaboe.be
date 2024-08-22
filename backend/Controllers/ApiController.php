<?php

namespace Controllers;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use stdClass;

class ApiController extends stdClass
{
	const VALIDATION_STATE_VALID = "valid";
	const VALIDATION_STATE_INVALID = "invalid";

	private $httpCode = 200;
	private $validation = [];
	private $reload = false;
	private $toast = [];

	private $json = [];

	public function handle()
	{
		if ($this->httpCode) Helpers::response()->httpCode($this->httpCode);

		if ($this->validation) Arrays::setNestedValue($this->json, ['validation'], $this->validation);
		if ($this->redirect) Arrays::setNestedValue($this->json, ['redirect'], $this->redirect);
		if ($this->error) Arrays::setNestedValue($this->json, ['error'], $this->error);
		if ($this->toast) Arrays::setNestedValue($this->json, ['toast'], $this->toast);
		if ($this->reload) Arrays::setNestedValue($this->json, ['reload'], $this->reload);
		if ($this->closeModal) Arrays::setNestedValue($this->json, ['closeModal'], $this->closeModal);
		if ($this->reloadTable) Arrays::setNestedValue($this->json, ['reloadTable'], $this->reloadTable);
		if ($this->reloadCalendar) Arrays::setNestedValue($this->json, ['reloadCalendar'], $this->reloadCalendar);
		if ($this->resetForm) Arrays::setNestedValue($this->json, ['resetForm'], $this->resetForm);
		if ($this->returnToStep) Arrays::setNestedValue($this->json, ['returnToStep'], $this->returnToStep);

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

	protected function setError($error)
	{
		$this->error = $error;
	}

	protected function setToast($title, $message, $type = self::VALIDATION_STATE_VALID)
	{
		$this->toast[] = [
			"title" => $title,
			"type" => $type,
			"message" => $message
		];
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

	protected function setCloseModal($id = null)
	{
		$this->closeModal = $id ?? true;
	}

	protected function setReloadTable($id = null)
	{
		$this->reloadTable = $id ?? true;
	}

	protected function setReloadCalendar($id = null)
	{
		$this->reloadCalendar = $id ?? true;
	}

	protected function setResetForm()
	{
		$this->resetForm = true;
	}

	protected function setReturnToStep()
	{
		$this->returnToStep = true;
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
