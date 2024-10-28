<?php

namespace Informat\SOAP\Repository;

use Informat\SOAP\Interface\Repository;

class Leerkrachten extends Repository
{
	public function __construct()
	{
		parent::__construct("Leerkrachten", INFORMAT_CURRENT_SCHOOLYEAR, \Informat\SOAP\Object\Leerkrachten::class, idField: 'p_persoon', orderField: 'Naam');
		$this->setExtraGetData("personeelsgroep_is_een_optie", "");
	}
}
