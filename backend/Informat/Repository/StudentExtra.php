<?php

namespace Informat\Repository;

use Informat\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class StudentExtra extends Repository
{
	public function __construct()
	{
		parent::__construct("LlnExtra", INFORMAT_CURRENT_SCHOOLYEAR, "110064", \Informat\Object\StudentExtra::class, idField: 'pointer', orderField: 'naam');
		$this->setExtraGetData("referentiedatum", "");
		$this->setExtraGetData("hoofdstructuur", "");
	}
}
