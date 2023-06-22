<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class StudentSubscription extends CustomObject
{
	protected $objectAttributes = [
		"PInschrijving",
		"PPersoon",
		"Status",
		"Begindatum",
		"Einddatum",
		"Instelnr",
		"School",
		"VestCode",
		"Vestiging",
		"AfdCode",
		"Afdeling",
		"Afdelingsjaar",
		"NrAdmgr",
		"Graad",
		"Leerjaar",
		"Financierbaarheid",
		"Levensbeschouwing",
		"CapaciteitNaam",
		"PreRegistrationId"
	];
}
