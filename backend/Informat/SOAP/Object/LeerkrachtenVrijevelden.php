<?php

namespace Informat\SOAP\Object;

use Informat\SOAP\Interface\CustomObject;

class LeerkrachtenVrijevelden extends CustomObject
{
	protected $objectAttributes = [
		"pPersoon" => "int",
		"OmschrijvingVrijVeld" => "string",
		"WaardeVrijVeld" => "string",
		"Rubriek" => "string"
	];
}
