<?php

namespace Informat\Object;

use Informat\Interface\CustomObject;

class Staff extends CustomObject
{
	protected $objectAttributes = [
		"p_persoon",
		"Stamnummer",
		"Naam",
		"Voornaam",
		"Geboortedatum",
		"Geboorteplaats",
		"Geslacht",
		"Rijksregnr",
		"Diploma",
		"Schooljaar",
		"Instelnr",
		"Thuistelefoon",
		"Gsm",
		"Prive_email",
		"School_email",
		"Opmerking",
		"Actief",
		"Gebruikersnaam",
		"Wachtwoord",
		"GebruikersnaamAd",
		"WachtwoordAd",
		"Straat",
		"Nr",
		"Bus",
		"Dlpostnr",
		"Dlgem",
		"Landcode",
		"Adres",
		"Iban",
		"Bic"
	];

	public function init()
	{
		$this->removeArray();

		if ($this->Geslacht == 1) $this->GeslachtForDB = "M";
		else if ($this->Geslacht == 2) $this->GeslachtForDB = "F";
		else $this->GeslachtForDB = "X";

		if ($this->Actief == "J") $this->ActiefForDB = 1;
		else $this->ActiefForDB = 0;
	}

	private function removeArray()
	{
		foreach ($this->objectAttributes as $attr) {
			if (is_array($this->$attr)) $this->$attr = null;
		}
	}
}
