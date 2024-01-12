<?php

namespace Informat\Repository;

use Informat\Interface\Repository;

class StaffFreeField extends Repository
{
	public function __construct()
	{
		parent::__construct("LeerkrachtenVrijevelden", INFORMAT_CURRENT_SCHOOLYEAR, \Informat\Object\StaffFreeField::class, idField: 'pPersoon', orderField: 'Personeelslid');
	}
}
