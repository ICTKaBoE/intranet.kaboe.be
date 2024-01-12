<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class StaffFreeField extends CustomObject
{
	protected $objectAttributes = [
		"pPersoon",
		"Personeelslid",
		"Stamnummer",
		"OmschrijvingVrijVeld",
		"WaardeVrijVeld",
		"Datatype",
		"Rubriek"
	];
}
