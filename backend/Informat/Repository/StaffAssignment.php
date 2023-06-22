<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class StaffAssignment extends Repository
{
	public function __construct()
	{
		parent::__construct("LeerkrachtenOpdrachten", INFORMAT_CURRENT_SCHOOLYEAR, "110064", \Informat\Object\StaffAssignment::class, idField: 'POpdr', orderField: 'Begindatum');
		$this->setExtraGetData("ReferentiedatumNPRE", "");
		$this->setExtraGetData("Met_NSU_Uren", "");
	}
}
