<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\OrderSupplier as ObjectOrderSupplier;
use Database\Repository\OrderSupplier;

class OrderController extends ApiController
{
	public function supplier($prefix, $method, $id = null)
	{
		$name = Helpers::input()->post('name')->getValue();
		$email = Helpers::input()->post('email')->getValue();
		$phone = Helpers::input()->post('phone')?->getValue();
		$contact = Helpers::input()->post('contact')->getValue();
		$delete = Strings::equal($method, "delete");

		if (!$delete) {
			if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($email) || Input::empty($email)) $this->setValidation("email", "E-mail moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			if (!Input::check($contact) || Input::empty($contact)) $this->setValidation("contact", "Contactpersoon moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		}

		if ($this->validationIsAllGood()) {
			$repo = new OrderSupplier;
			$supplier = is_null($id) ? new ObjectOrderSupplier : $repo->get($id)[0];

			if (!empty($repo->checkAlreadyExist($name, $id))) {
				$this->setValidation("name", "Er bestaat al een leverancier met deze naam!", self::VALIDATION_STATE_INVALID);
			} else {
				if (!$delete) {
					$supplier->name = $name;
					$supplier->email = $email;
					$supplier->phone = $phone;
					$supplier->contact = $contact;
				} else $supplier->deleted = true;

				$repo->set($supplier);
			}
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/orders/suppliers");
		$this->handle();
	}
}
