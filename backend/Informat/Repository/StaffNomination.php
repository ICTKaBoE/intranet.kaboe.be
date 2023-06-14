<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class Staff extends Repository
{
	public function __construct()
	{
		parent::__construct("LeerkrachtenBenoemingen", INFORMAT_CURRENT_SCHOOLYEAR, "110064", \Informat\Object\StaffNomination::class, idField: 'PBenoeming', orderField: 'Ingangsdatum');
	}
}
