<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class StudentSubscription extends Repository
{
	public function __construct()
	{
		parent::__construct("Inschrijvingen", INFORMAT_CURRENT_SCHOOLYEAR, \Informat\Object\StudentSubscription::class, idField: 'PInschrijvingen', orderField: 'Begindatum');
		$this->setExtraGetData("hoofdstructuur", "");
		$this->setExtraGetData("preRegistrationId", "");
	}
}
