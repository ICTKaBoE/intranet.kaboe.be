<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class Student extends CustomObject
{
	protected $objectAttributes = [
		"p_persoon",
		"Naam",
		"Voornaam",
		"geslacht",
		"geboortedatum",
		"geboorteplaats",
		"rijksregnr",
		"stamnr",
		"Stamnr_kort",
		"naamMa",
		"voornaamMa",
		"OverledenMa",
		"naamPa",
		"voornaamPa",
		"overledenPa",
		"instelnr",
		"school",
		"vestcode",
		"vestiging",
		"begindatum",
		"einddatum",
		"afdeling",
		"afdcode",
		"afdelingsjaar",
		"Klas",
		"Klasnr",
		"nr_admgr",
		"graad",
		"leerjaar",
		"voorlopig",
		"fietsnummer",
		"Klascode",
		"Klasnaam",
		"Financierbaarheid",
		"Levensbeschouwing",
		"PInschrijving",
		"PAdfjaar",
		"PInschKlas",
		"PKlas"
	];
}
