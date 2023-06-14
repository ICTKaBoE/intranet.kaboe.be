<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class StaffNomination extends CustomObject
{
	protected $objectAttributes = [
		"PPersoon",
		"Personeelslid",
		"Stamnummer",
		"Instelnr",
		"Hoofdstructuur",
		"PBenoeming",
		"Ambtcode",
		"Ambtomschrijving",
		"Vakcode",
		"Vakomschrijving",
		"Gelijkvakcode",
		"Gelijkvakomschrijving",
		"Ingangsdatum",
		"Einddatum",
		"Teller",
		"Noemer",
		"Bekwaamheidsbewijs",
		"Graad",
		"Onderwijsvorm",
		"Opleidingsvorm",
		"Opmerking",
	];
}
