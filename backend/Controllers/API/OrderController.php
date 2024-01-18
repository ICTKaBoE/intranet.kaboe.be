<?php

namespace Controllers\API;

use Security\User;
use Mail\OrderMail;
use Router\Helpers;
use Security\Input;
use Helpers\Mapping;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Log;
use Controllers\ApiController;
use Database\Repository\Order;
use Database\Repository\Module;
use Database\Repository\School;
use Database\Repository\Supplier;
use Database\Repository\OrderLine;
use Database\Repository\ModuleSetting;
use Database\Object\Order as ObjectOrder;
use Database\Object\OrderLine as ObjectOrderLine;
use Database\Object\ModuleSetting as ObjectModuleSetting;

class OrderController extends ApiController
{
	public function getDashboard($view, $id = null)
	{
		if ($view == "chart") {
			$schoolRepo = new School;
			$orderRepo = new Order;
			$allstatus = Mapping::get("order/status");

			$this->appendToJson(["xaxis", "categories"], Arrays::map($schoolRepo->get(), fn ($s) => $s->name));
			$series = [];

			foreach ($allstatus as $status) {
				$myarray = array("name" => array_values($status)[0]);
				array_push($series, $myarray);
			}

			foreach ($schoolRepo->get() as $idx => $school) {
				for ($i = 0; $i < count($allstatus); $i++) {
					$series[$i]["data"][$idx] = count($orderRepo->getBySchoolByStatus($school->id, array_keys($allstatus)[$i]));
				}
			}
			$this->appendToJson("series", $series);
		}
		$this->handle();
	}

	public function getOrders($view, $type, $id = null)
	{
		if ($view == "table") {
			$schoolId = Helpers::url()->getParam("school");
			$status = Helpers::url()->getParam("status");

			$filters = [];

			if (Input::check($schoolId, Input::INPUT_TYPE_INT) && !Input::empty($schoolId)) $filters['schoolId'] = $schoolId;
			if (Input::check($status) && !Input::empty($status)) $filters['status'] = $status;

			$rows = (new Order)->getByTypeWithFilters($type, $filters);

			$this->appendToJson(
				'columns',
				[
					[
						"type" => "checkbox",
						"class" => ["w-1"],
						"data" => "id"
					],
					[
						"title" => "Nummer",
						"data" => "number",
						"width" => 120
					],
					[
						"type" => "badge",
						"title" => "Status",
						"data" => "statusFull",
						"backgroundColor" => "statusColor",
						"width" => 200
					],
					[
						"type" => "badge",
						"title" => "School",
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 100
					],
					[
						"title" => "Aangemaakt door",
						"data" => "creator.fullName",
						"width" => 200
					],
					[
						"title" => "Goed te keuren door",
						"data" => "acceptor.fullName",
						"width" => 200
					],
					[
						"title" => "Leverancier",
						"data" => "supplier.nameWithMainContact",
						"width" => 300
					],
					[
						"title" => "Korte beschrijving",
						"data" => "descriptionNoHtml",
						"format" => [
							"length" => 200
						]
					]
				]
			);

			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", $rows);
		} else if ($view == "form") $this->appendToJson(["fields"], Arrays::firstOrNull((new Order)->get($id)));

		$this->handle();
	}

	public function getOrderLines($view, $type, $id = null)
	{
		if ($view == "table") {
			$orderId = Helpers::input()->get("orderId");
			$rows = ($orderId ? (new OrderLine)->getByOrder($orderId) : (Session::get("orderLinesNew") ?? []));

			$this->appendToJson(
				'columns',
				[
					[
						"type" => "checkbox",
						"class" => ["w-1"],
						"data" => "id"
					],
					[
						"title" => "#",
						"data" => "amount",
						"width" => 50
					],
					[
						"title" => "Wat",
						"data" => "what",
						"width" => 200
					],
					[
						"title" => "Voor",
						"data" => "forDescription",
						"width" => 100
					],
					[
						"title" => "Toestel",
						"data" => "asset.shortDescription"
					],
					[
						"title" => "Reden",
						"data" => "reason"
					],
					[
						"title" => "Offerteprijs (per stuk)",
						"data" => "quotationVatIncludedText",
						"width" => 100
					]
				]
			);

			Arrays::each($rows, fn ($r) => $r->link());
			$this->appendToJson("rows", $rows);
		} else if ($view == "form") $this->appendToJson(["fields"], Arrays::firstOrNull((new OrderLine)->get($id)));

		$this->handle();
	}

	public function getSettings($view)
	{
		$module = (new Module)->getByModule("orders");
		$settings = (new ModuleSetting)->getByModule($module->id);

		$returnSettings = [];
		foreach ($settings as $setting) $returnSettings[$setting->key] = $setting->value;

		$this->appendToJson("fields", $returnSettings);
		$this->handle();
	}

	// POST
	public function postOrder($view, $id = null)
	{
		$stepCheck = Helpers::input()->get("stepCheck", false);

		$status = Helpers::input()->post('status')->getValue();
		$schoolId = Helpers::input()->post('schoolId')->getValue();
		$acceptorId = Helpers::input()->post('acceptorId')->getValue();
		$supplierId = Helpers::input()->post('supplierId')->getValue();
		$description = Helpers::input()->post('description')->getValue();
		$faction = Helpers::input()->post('faction', false)->getValue();

		$repo = new Order;

		if ($faction !== "delete") {
			if (!Input::check($status) || Input::empty($status)) {
				$this->setValidation("status", "Status moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Status is not filled in");
			}

			if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
				$this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
			}

			if (!Input::check($acceptorId, Input::INPUT_TYPE_INT) || Input::empty($acceptorId)) {
				$this->setValidation("acceptorId", "Goedkeurder moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Acceptor is not filled in");
			}

			if (!Input::check($supplierId, Input::INPUT_TYPE_INT) || Input::empty($supplierId)) {
				$this->setValidation("supplierId", "Leverancier moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Supplier is not filled in");
			}

			if (!Input::check($description) || Input::empty($description)) {
				$this->setValidation("description", "Beschrijving moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Description is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$order = is_null($id) ? new ObjectOrder : $repo->get($id)[0];

				$order->status = $status;
				$order->schoolId = $schoolId;
				if (is_null($id)) $order->creatorId = User::getLoggedInUser()->id;
				$order->acceptorId = $acceptorId;
				$order->supplierId = $supplierId;
				$order->description = $description;
				$newOrder = $repo->set($order);

				Log::write(description: "Added/Edited order with id " . (is_null($id) ? $newOrder : $id));
				if ($stepCheck) $this->appendToJson(['setId'], (is_null($id) ? $newOrder : $id));
			} else {
				if ($stepCheck) $this->setReturnToStep();
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$order = Arrays::firstOrNull($repo->get($_id));

				if (!is_null($order)) {
					$order->deleted = 1;
					$repo->set($order);

					Log::write(description: "Deleted order {$order->number} with id {$order->id}");
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			if (!$stepCheck) {
				$this->setCloseModal();
				$this->setReloadTable();
				if ($faction !== "delete") $this->setToast("Bestellingen - Overzicht", "De bestelling is opgeslagen!");
				else $this->setToast("Bestellingen - Overzicht", "De bestelling(en) is/zijn verwijderd!");
			}
		}

		$this->handle();
	}

	public function postOrderAcceptDeny($view, $id)
	{
		$status = Helpers::input()->post('status')->getValue();

		$repo = new Order;

		$order = $repo->get($id)[0];

		$order->status = $status;
		$repo->set($order);

		(new OrderMail)->sendAcceptDenyMail($order);

		Log::write(description: User::getLoggedInUser()->fullName . " accepted/denied order with id {$id}");

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
			$this->setToast("Bestellingen - Door mij goed te keuren", "De bestelling is goedgekeurd/geweigerd!");
		}

		$this->handle();
	}

	public function postOrderLine($view, $type, $id = null)
	{
		$orderId = Helpers::input()->post("orderId")->getValue();
		$amount = Helpers::input()->post('amount')->getValue();
		$for = Helpers::input()->post('for')->getValue();
		$assetId = Helpers::input()->post('assetId')?->getValue();
		$what = Helpers::input()->post('what')->getValue();
		$reason = Helpers::input()->post('reason')->getValue();
		$quotationPrice = Helpers::input()->post('quotationPrice')?->getValue();
		$quotationVatIncluded = Helpers::input()->post('quotationVatIncluded', 'off')?->getValue();
		$warenty = Helpers::input()->post('warenty', 'off')?->getValue();

		$quotationVatIncluded = Input::convertToBool($quotationVatIncluded);
		$warenty = Input::convertToBool($warenty);

		$faction = Helpers::input()->post('lfaction', false)->getValue();

		$repo = new OrderLine;

		if ($faction !== "delete") {
			if (!Input::check($amount, Input::INPUT_TYPE_INT) || Input::empty($amount)) {
				$this->setValidation("amount", "Aantal moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Amount is not filled in");
			}

			if (!Input::check($for) || Input::empty($for)) {
				$this->setValidation("for", "Voor moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "For is not filled in");
			}

			if (Arrays::contains(["L", "D", "P", "B"], $for) && (!Input::check($assetId, Input::INPUT_TYPE_INT) || Input::empty($assetId))) {
				$this->setValidation("assetId", "Toestel moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Asset is not filled in");
			}

			if (!Input::check($what) || Input::empty($what)) {
				$this->setValidation("what", "Wat is er nodig moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "What is not filled in");
			}

			if (!Input::check($reason) || Input::empty($reason)) {
				$this->setValidation("reason", "Reden moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Reason is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$orderLine = is_null($id) ? new ObjectOrderLine : $repo->get($id)[0];

				$orderLine->orderId = $orderId;
				$orderLine->amount = $amount;
				$orderLine->for = $for;
				$orderLine->assetId = $assetId;
				$orderLine->what = $what;
				$orderLine->reason = $reason;
				$orderLine->quotationPrice = $quotationPrice;
				$orderLine->quotationVatIncluded = $quotationVatIncluded;
				$orderLine->warenty = $warenty;
				$newOrderLine = $repo->set($orderLine);

				Log::write(description: "Added/Edited orderline with id " . (is_null($id) ? $newOrderLine : $id));
			}
		} else {
			$ids = Helpers::input()->post('lids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$orderLine = Arrays::firstOrNull($repo->get($_id));

				if (!is_null($orderLine)) {
					$orderLine->deleted = 1;
					$repo->set($orderLine);

					Log::write(description: "Deleted orderline with id {$orderLine->id}");
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal("order-line");
			$this->setReloadTable();
			if ($faction !== "delete") $this->setToast("Bestellingen - Overzicht - Lijn", "De bestellijn is opgeslagen!");
			else $this->setToast("Bestellingen - Overzicht - Lijn", "De bestellijn(en) is/zijn verwijderd!");
		}

		$this->handle();
	}

	public function postRequest($view, $what)
	{
		if ($what == "quote") $this->postRequestQuote();
		else if ($what == "accept") $this->postRequestAccept();
		else if ($what == "post") $this->postThisOrder();

		$this->setCloseModal();
		$this->setReloadTable();
		$this->handle();
	}

	public function postRequestQuote()
	{
		$ids = Helpers::input()->post('rqids')->getValue();

		$orderRepo = new Order;
		$orderLineRepo = new OrderLine;
		$orderMail = new OrderMail;

		foreach (explode("-", $ids) as $id) {
			$order = Arrays::firstOrNull($orderRepo->get($id));
			if (is_null($order)) continue;

			$orderLines = $orderLineRepo->getByOrder($id);
			if (empty($orderLines)) continue;

			$order->link();
			Arrays::each($orderLines, fn ($ol) => $ol->link(true));

			$orderMail->sendQuotationMail($order, $orderLines);
			$order->status = "QR";
			$orderRepo->set($order);
		}
	}

	public function postRequestAccept()
	{
		$ids = Helpers::input()->post('raids')->getValue();

		$orderRepo = new Order;
		$orderLineRepo = new OrderLine;
		$orderMail = new OrderMail;

		foreach (explode("-", $ids) as $id) {
			$order = Arrays::firstOrNull($orderRepo->get($id));
			if (is_null($order)) continue;

			$orderLines = $orderLineRepo->getByOrder($id);
			if (empty($orderLines)) continue;

			$order->link();

			$orderMail->sendAcceptMail($order);
			$order->status = "W";
			$orderRepo->set($order);
		}
	}

	public function postThisOrder()
	{
		$ids = Helpers::input()->post('poids')->getValue();

		$orderRepo = new Order;
		$orderLineRepo = new OrderLine;
		$orderMail = new OrderMail;

		foreach (explode("-", $ids) as $id) {
			$order = Arrays::firstOrNull($orderRepo->get($id));
			if (is_null($order)) continue;

			$orderLines = $orderLineRepo->getByOrder($id);
			if (empty($orderLines)) continue;

			Arrays::each($orderLines, fn ($ol) => $ol->link(true));
			$order->link();

			$orderMail->sendPostMail($order, $orderLines);
			$order->status = "O";
			$orderRepo->set($order);
		}
	}

	public function postSettings()
	{
		$module = (new Module)->getByModule('orders');
		$moduleSettingRepo = new ModuleSetting;

		foreach (DEFAULT_SETTINGS["orders"] as $setting => $defaultValue) {
			$moduleSetting = $moduleSettingRepo->getByModuleAndKey($module->id, $setting);
			$value = isset($_POST[$setting]) ? Helpers::input()->post($setting)->getValue() : $defaultValue;

			if (is_null($moduleSetting)) {
				$moduleSetting = new ObjectModuleSetting([
					'moduleId' => $module->id,
					'key' => $setting,
					'value' => Input::convertToBool($value)
				]);
			} else {
				$moduleSetting->value = Input::convertToBool($value);
			}

			$moduleSettingRepo->set($moduleSetting);
		}

		Log::write(description: "Changed settings for orders");

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else {
			$this->setToast("Bestellingen - Instellingen", "De instellingen zijn opgeslagen!");
		}
		$this->handle();
	}
}
