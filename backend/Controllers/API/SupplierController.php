<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Arrays;
use Controllers\ApiController;
use Database\Object\Supplier as ObjectSupplier;
use Database\Object\SupplierContact as ObjectSupplierContact;
use Database\Repository\Log;
use Database\Repository\Supplier;
use Database\Repository\SupplierContact;

class SupplierController extends ApiController
{
	// GET
	public function getSuppliers($view, $id = null)
	{
		$suppliers = (is_null($id) ? (new Supplier)->get() : (new Supplier)->get($id));
		Arrays::each($suppliers, fn ($s) => $s->link()->init());

		if ($view == "table") {
			$this->appendToJson(
				'columns',
				[
					[
						"type" => "checkbox",
						"class" => ["w-1"],
						"data" => "id"
					],
					[
						"title" => "Naam",
						"data" => "name",
						"width" => 200
					],
					[
						"title" => "E-mail",
						"data" => "email",
						"width" => 300
					],
					[
						"title" => "Telefoon",
						"data" => "phone",
						"width" => 150
					],
					[
						"title" => "Adres",
						"data" => "formattedAddress"
					]
				]
			);

			$this->appendToJson(['rows'], $suppliers);
		} else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull($suppliers));
		else if ($view == "select") $this->appendToJson(['items'], $suppliers);

		$this->handle();
	}

	public function getContacts($view, $id = null)
	{
		$contacts = (is_null($id) ? (new SupplierContact)->get() : Arrays::firstOrNull((new SupplierContact)->get($id)));

		if (
			$view == "table"
		) {
			$this->appendToJson(
				'columns',
				[
					[
						"type" => "checkbox",
						"class" => ["w-1"],
						"data" => "id"
					],
					[
						"type" => "icon",
						"title" => "Hoofdcontact",
						"data" => "isMainContactIcon",
						"class" => ["w-1"]
					],
					[
						"title" => "Leverancier",
						"data" => "supplier.name",
						"width" => 200
					],
					[
						"title" => "Naam",
						"data" => "fullName",
						"width" => 200
					],
					[
						"title" => "E-mail",
						"data" => "email",
						"width" => 300
					],
					[
						"title" => "Telefoon",
						"data" => "phone"
					]
				]
			);

			Arrays::each($contacts, fn ($c) => $c->link());
			$this->appendToJson(['rows'], $contacts);
		} else if ($view == "form") $this->appendToJson(['fields'], $contacts);
		else if ($view == "select") $this->appendToJson(['items'], $contacts);

		$this->handle();
	}

	// POST
	public function postSuppliers($view, $id = null)
	{
		$name = Helpers::input()->post('name')->getValue();
		$email = Helpers::input()->post('email')?->getValue();
		$phone = Helpers::input()->post('phone')?->getValue();
		$street = Helpers::input()->post('street')?->getValue();
		$number = Helpers::input()->post('number')?->getValue();
		$bus = Helpers::input()->post('bus')?->getValue();
		$zipcode = Helpers::input()->post('zipcode')?->getValue();
		$city = Helpers::input()->post('city')?->getValue();
		$country = Helpers::input()->post('country')?->getValue();

		$faction = Helpers::input()->post('faction', false)->getValue();

		$repo = new Supplier;

		if ($faction !== "delete") {
			if (!Input::check($name) || Input::empty($name)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}
			if (!Input::check($email) || Input::empty($email)) {
				$this->setValidation("email", "Email moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Email is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$supplierpage = is_null($id) ? new ObjectSupplier() : $repo->get($id)[0];

				$supplierpage->name = $name;
				$supplierpage->email = $email;
				$supplierpage->phone = $phone;
				$supplierpage->street = $street;
				$supplierpage->number = $number;
				$supplierpage->bus = $bus;
				$supplierpage->zipcode = $zipcode;
				$supplierpage->city = $city;
				$supplierpage->country = $country;

				$newSupplier = $repo->set($supplierpage);

				Log::write(description: "Added supplier {$name} with id " . (is_null($id) ? $newSupplier : $id));
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$supplier = Arrays::firstOrNull($repo->get($_id));

				if (!is_null($supplier)) {
					$supplier->deleted = 1;
					$repo->set($supplier);

					Log::write(description: "Deleted supplier {$supplier->name} with id {$supplier->id}");
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
			if ($faction !== "delete") $this->setToast("Leveranciers - Overzicht", "De leverancier is opgeslagen!");
			else $this->setToast("Leveranciers - Overzicht", "De leveranciers is/zijn verwijderd!");
		}

		$this->handle();
	}

	public function postContacts($view, $id = null)
	{
		$supplierId = Helpers::input()->post('supplierId')->getValue();
		$name = Helpers::input()->post('name')->getValue();
		$firstName = Helpers::input()->post('firstName')->getValue();
		$email = Helpers::input()->post('email')?->getValue();
		$phone = Helpers::input()->post('phone')?->getValue();

		$faction = Helpers::input()->post('faction', false)->getValue();

		$repo = new SupplierContact;

		if ($faction !== "delete") {
			if (!Input::check($supplierId, Input::INPUT_TYPE_INT) || Input::empty($supplierId)) {
				$this->setValidation("supplierId", "Leverancier moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Supplier ID is not filled in");
			}
			if (!Input::check($name) || Input::empty($name)) {
				$this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Name is not filled in");
			}

			if ($this->validationIsAllGood()) {
				$contactpage = is_null($id) ? new ObjectSupplier() : $repo->get($id)[0];
				$contactpage->supplierId = $supplierId;
				$contactpage->name = $name;
				$contactpage->firstName = $firstName;
				$contactpage->email = $email;
				$contactpage->phone = $phone;

				$newContact = $repo->set($contactpage);

				Log::write(description: "Added/Edited contact {$name} with id " . (is_null($id) ? $newContact : $id));
			}
		} else {
			$ids = Helpers::input()->post('ids')->getValue();
			$ids = explode("-", $ids);

			foreach ($ids as $_id) {
				$contact = Arrays::firstOrNull($repo->get($_id));

				if (!is_null($contact)) {
					$contact->deleted = 1;
					$repo->set($contact);

					Log::write(description: "Deleted contact {$contact->name} with id {$contact->id}");
				}
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		else {
			$this->setCloseModal();
			$this->setReloadTable();
			if ($faction !== "delete") $this->setToast("Leveranciers - Contacten", "Het contact is opgeslagen!");
			else $this->setToast("Leveranciers - Contacten", "De contacten is/zijn verwijderd!");
		}

		$this->handle();
	}
}
