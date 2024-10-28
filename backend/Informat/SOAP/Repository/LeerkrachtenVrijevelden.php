<?php

namespace Informat\SOAP\Repository;

use Informat\SOAP\Interface\Repository;

class LeerkrachtenVrijevelden extends Repository
{
	public function __construct()
	{
		parent::__construct("LeerkrachtenVrijevelden", INFORMAT_CURRENT_SCHOOLYEAR, \Informat\SOAP\Object\LeerkrachtenVrijevelden::class, idField: false, orderField: 'OmschrijvingVrijVeld');
	}
}
