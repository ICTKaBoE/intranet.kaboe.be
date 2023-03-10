<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class Staff extends Repository
{
	public function __construct()
	{
		parent::__construct("Leerkrachten", INFORMAT_CURRENT_SCHOOLYEAR, "110064", \Informat\Object\Staff::class, idField: 'p_persoon', orderField: 'Naam');
		$this->setExtraGetData("personeelsgroep_is_een_optie", "");
	}
}
