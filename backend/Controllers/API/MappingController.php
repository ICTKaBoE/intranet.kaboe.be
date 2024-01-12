<?php

namespace Controllers\API;

use Helpers\Mapping;
use Controllers\ApiController;

class MappingController extends ApiController
{
	public function helpdeskPriority($view)
	{
		$_items = Mapping::get("helpdesk/priority");

		if ($view == "select") {
			$items = [];

			foreach ($_items as $item => $rest) {
				$items[] = [
					"id" => $item,
					...$rest
				];
			}

			$this->appendToJson("items", $items);
		}

		$this->handle();
	}

	public function helpdeskStatus()
	{
		$_items = Mapping::get("helpdesk/status");
		$items = [];

		foreach ($_items as $item => $rest) {
			$items[] = [
				"id" => $item,
				...$rest
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function helpdeskType()
	{
		$_items = Mapping::get("helpdesk/type");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function helpdeskSubtype()
	{
		$_items = Mapping::get("helpdesk/subtype");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function ordersStatus()
	{
		$_items = Mapping::get("order/status");
		$items = [];

		foreach ($_items as $item => $rest) {
			$items[] = [
				"id" => $item,
				...$rest
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function ordersLineFor()
	{
		$_items = Mapping::get("order/line/for");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function managementComputerType()
	{
		$_items = Mapping::get("management/computer/type");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function managementOsType()
	{
		$_items = Mapping::get("management/computer/osType");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function managementOsArchitecture()
	{
		$_items = Mapping::get("management/computer/osArchitecture");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function managementPrinterColormode()
	{
		$_items = Mapping::get("management/printer/colormode");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function managementCartType()
	{
		$_items = Mapping::get("management/cart/type");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}

	public function reservationType($view)
	{
		$_items = Mapping::get("reservation/type");

		if ($view == "select") {
			$items = [];

			foreach ($_items as $item => $rest) {
				$items[] = [
					"id" => $item,
					...$rest
				];
			}

			$this->appendToJson("items", $items);
		}

		$this->handle();
	}

	public function libraryCategory()
	{
		$_items = Mapping::get("schoollibrary/category");
		$items = [];

		foreach ($_items as $key => $value) {
			$items[] = [
				"id" => $key,
				"description" => $value
			];
		}

		$this->appendToJson("items", $items);
		$this->handle();
	}
}
