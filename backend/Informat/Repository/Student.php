<?php

namespace Informat\Repository;

use Informat\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Student extends Repository
{
	public function __construct()
	{
		parent::__construct("Lln", INFORMAT_CURRENT_SCHOOLYEAR, "110064", \Informat\Object\Student::class, idField: 'p_persoon', orderField: 'Naam');
		$this->setExtraGetData("referentiedatum", "");
		$this->setExtraGetData("hoofdstructuur", "");
	}

	public function getByRRN($rrn = null)
	{
		$rrn = preg_replace('/[^0-9]/', "", $rrn);
		$items = $this->get(order: true);
		if (!is_null($rrn) && Strings::isNotBlank($rrn)) $items = Arrays::filter($items, fn ($i) => Strings::equal($i->rijksregnr, $rrn));

		return Arrays::firstOrNull($items);
	}

	public function getByInstituteNumber($iNumber)
	{
		$this->setInstituteNumber($iNumber);
		$items = $this->get();

		return $items;
	}
}
