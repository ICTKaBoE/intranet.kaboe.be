<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class StudentSubgroup extends CustomObject
{
	protected $objectAttributes = [
		"p_persoon",
		"Naam",
		"Voornaam",
		"School",
		"Instelnr",
		"Vestcode",
		"Vestiging",
		"Klas",
		"BegindatumKlas",
		"EinddatumKlas",
		"Groeptype",
		"Klasnr",
		"Klastitularis",
		"Klascode",
		"Status",
		"PKlas",
		"PInschrklas",
		"PInschrijving",
		"Graad",
		"Leerjaar",
	];
}
