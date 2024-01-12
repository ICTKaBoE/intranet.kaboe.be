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
	}

	private function removeArray()
	{
		foreach ($this->objectAttributes as $attr) {
			if (is_array($this->$attr)) $this->$attr = null;
		}
	}
}
