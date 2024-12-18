<?php

namespace Controllers;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\Code;
use stdClass;

class ApiController extends stdClass
{
	const VALIDATION_STATE_VALID = "valid";
	const VALIDATION_STATE_INVALID = "invalid";

	const VIEW_FORM = "form";
	const VIEW_CALENDAR = "calendar";
	const VIEW_TABLE = "table";
	const VIEW_SELECT = "select";
	const VIEW_CHART = "chart";
	const VIEW_LIST = "list";

	private $httpCode = 200;
	private $validation = [];
	private $reload = false;
	private $toast = [];

	private $json = [];

	public function any($view = null, $what = null, $id = null)
	{
		Code::noTimeLimit();

		$method = Helpers::request()->getMethod();
		$what = explode("-", $what);
		$what = Arrays::map($what, fn($w) => ucfirst($w));
		$what = implode("", $what);
		$function = $method . ucfirst($what ?: "list");

		if (method_exists($this, $function)) $this->$function($view, $id);
		else {
			$this->setHttpCode(400);
			$this->setError("Function '{$function}' not found in '" . $this::class . "'!");
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		$this->handle();
	}

	public function handle()
	{
		if ($this->httpCode) Helpers::response()->httpCode($this->httpCode);

		if ($this->validation) Arrays::setNestedValue($this->json, ['validation'], $this->validation);
		if ($this->redirect) Arrays::setNestedValue($this->json, ['redirect'], $this->redirect);
		if ($this->return) Arrays::setNestedValue($this->json, ['return'], $this->return);
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

	protected function setToast($message, $type = self::VALIDATION_STATE_VALID)
	{
		$this->toast[] = [
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

	protected function setReturn()
	{
		$this->return = true;
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
		if (!is_null($key) && !empty($key)) {
			if (is_string($key)) $key = [$key];
			Arrays::setNestedValue($this->json, $key, $data);
		} else $this->json[] = $data;
	}

	protected function validationIsAllGood()
	{
		$validation = count($this->validation) == Arrays::count($this->validation, fn($v) => Strings::equal($v['state'], self::VALIDATION_STATE_VALID));
		$toast = count($this->toast) == Arrays::count($this->toast, fn($t) => Strings::equal($t['type'], self::VALIDATION_STATE_VALID));
		$error = is_null($this->error);

		return ($validation && $toast && $error);
	}

	protected function getValidation()
	{
		return $this->validation;
	}
}
