<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class StudentSubgroup extends Repository
{
	public function __construct()
	{
		parent::__construct("Subgroepen", INFORMAT_CURRENT_SCHOOLYEAR, \Informat\Object\StudentSubgroup::class, idField: 'p_persoon', orderField: 'Naam');
		$this->setExtraGetData("hoofdstructuur", "");
	}
}
