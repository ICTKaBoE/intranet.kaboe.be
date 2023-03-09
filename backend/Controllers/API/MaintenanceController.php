<?php

namespace Controllers\API;

use Security\User;
use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Clock;
use Controllers\ApiController;
use Database\Repository\Maintenance;
use Database\Repository\UserProfile;
use Database\Object\Maintenance as ObjectMaintenance;
use Ouzo\Utilities\Strings;

class MaintenanceController extends ApiController
{
	public function setRequestStatus($prefix, $id, $status)
	{
		$repo = new Maintenance;
		$item = $repo->get($id)[0];

		if ($item) {
			$item->status = $status;
			$item->lastActionDateTime = Clock::nowAsString("Y-m-d H:i:s");
			$repo->set($item);
		}

		$this->handle();
	}

	public function request($prefix, $method, $id = null)
	{
		$subject = Helpers::input()->post('subject')->getValue();
		$priority = Helpers::input()->post('priority')->getValue();
		$location = Helpers::input()->post('location')->getValue();
		$details = Helpers::input()->post('details')->getValue();
		$executeBy = Helpers::input()->post('executeBy')->getValue();
		$finishedByDate = Helpers::input()->post('finishedByDate')->getValue();

		if (!Input::check($subject) && Input::empty($subject)) $this->setValidation("subject", "Onderwerp moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		if (!Input::check($priority) && Input::empty($priority)) $this->setValidation("priority", "Prioriteit moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

		if ($this->validationIsAllGood()) {
			$repo = new Maintenance;
			$request = $method == 'edit' ? $repo->get($id)[0] : new ObjectMaintenance;

			$request->schoolId = (new UserProfile)->getByUserId(User::getLoggedInUser()->id)->mainSchoolId;
			$request->creationDate = Clock::nowAsString("Y-m-d H:i:s");
			$request->lastActionDateTime = Clock::nowAsString("Y-m-d H:i:s");
			$request->finishedByDate = $finishedByDate;
			$request->priority = $priority;
			$request->location = Strings::isBlank($location) ? NULL : $location;
			$request->subject =  $subject;
			$request->details = Strings::isBlank($details) ? NULL : $details;
			$request->executeBy = Strings::isBlank($executeBy) ? NULL : $executeBy;

			$repo->set($request);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/maintenance/requests");
		$this->handle();
	}
}
