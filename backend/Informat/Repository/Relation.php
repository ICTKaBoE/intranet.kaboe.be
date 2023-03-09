<?php

namespace Informat\Repository;

use Informat\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Relation extends Repository
{
	public function __construct()
	{
		parent::__construct("Relaties", INFORMAT_CURRENT_SCHOOLYEAR, "110064", \Informat\Object\Relation::class, idField: 'PRelatie', orderField: 'Naam');
		$this->setExtraGetData("hoofdstructuur", "");
		$this->setExtraGetData("gewijzigdSinds", "");

		$this->setShiftKeyAndValue("PPersoon");
	}

	public function getByStudentId($studentId)
	{
		$items = $this->get(order: false);
		return Arrays::filter($items, fn ($i) => Strings::equal($i->PPersoon, $studentId));
	}
}
