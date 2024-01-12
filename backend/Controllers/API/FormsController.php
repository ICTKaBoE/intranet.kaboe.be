<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\TemperatureRegistration;
use Database\Repository\TemperatureRegistration as RepositoryTemperatureRegistration;

class FormsController extends ApiController
{
	public function index($view, $form)
	{
		if (Strings::equalsIgnoreCase($form, 'temperatureregistration')) $this->temperatureRegistration();

		$this->handle();
	}

	private function temperatureRegistration()
	{
		$schoolId = Helpers::input()->post('schoolId')->getValue();
		$person = Helpers::input()->post('person')->getValue();
		$soupTemp = Helpers::input()->post('soupTemp')->getValue();
		$potatoRicePastaTemp = Helpers::input()->post('potatoRicePastaTemp')->getValue();
		$vegetablesTemp = Helpers::input()->post('vegetablesTemp')->getValue();
		$meatFishTemp = Helpers::input()->post('meatFishTemp')->getValue();
		$description = Helpers::input()->post('description')->getValue();

		if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		if (!Input::check($person) || Input::empty($person)) $this->setValidation("person", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

		if ($this->validationIsAllGood()) {
			$now = Clock::nowAsString("Y-m-d H:i:s");

			$registration = new TemperatureRegistration;
			$registration->schoolId = $schoolId;
			$registration->creationDateTime = $now;
			$registration->person = $person;
			$registration->soupTemp = $soupTemp;
			$registration->potatoRicePastaTemp = $potatoRicePastaTemp;
			$registration->vegetablesTemp = $vegetablesTemp;
			$registration->meatFishTemp = $meatFishTemp;
			$registration->description = $description;

			(new RepositoryTemperatureRegistration)->set($registration);

			if (!$this->validationIsAllGood()) {
				$this->setHttpCode(400);
			} else {
				$this->setResetForm();
				$this->setToast("Formulier - Temperatuurregistratie Maaltijden", "De registratie werd goed opgeslagen!");
			}
		}
	}
}
