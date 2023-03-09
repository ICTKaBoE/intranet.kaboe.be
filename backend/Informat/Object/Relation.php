<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class Relation extends CustomObject
{
	protected $objectAttributes = [
		"PPersoon",
		"PRelatie",
		"Type",
		"Naam",
		"Voornaam",
		"Geslacht",
		"Insz",
		"Geboortedatum",
		"Overleden",
		"Beroep",
		"BurgerlijkeStaat",
		"Nationaliteit",
		"Lpv",
		"Opmerking",
		"Toegevoegd",
		"Gewijzigd",
		"RelatieUuid"
	];
}
