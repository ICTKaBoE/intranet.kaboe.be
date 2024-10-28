<?php

namespace Informat\SOAP\Object;

use Informat\SOAP\Interface\CustomObject;

class Leerkrachten extends CustomObject
{
	protected $objectAttributes = [
		"p_persoon" => "int",
		"Stamnummer" => "string",
		"Naam" => "string",
		"Voornaam" => "string",
		"Schooljaar" => "string",
		"Thuistelefoon" => "string",
		"Gsm" => "string",
		"Prive_email" => "string",
		"Actief" => "string",
		"Straat" => "string",
		"Nr" => "string",
		"Bus" => "string",
		"Dlpostnr" => "string",
		"Dlgem" => "string",
		"Landcode" => "string",
		"Iban" => "string"
	];
}
