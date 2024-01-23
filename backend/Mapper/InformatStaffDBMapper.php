<?php

namespace Mapper;

use Helpers\CString;
use Helpers\Mapping;
use Ouzo\Utilities\Clock;

class InformatStaffDBMapper extends MInterface
{
	protected $mapFields = [
		"informatUID" => "p_persoon",
		"masterNumber" => "Stamnummer",
		"name" => "Naam",
		"firstName" => "Voornaam",
		"birthPlace" => "Geboorteplaats",
		"birthDate" => "Geboortedatum",
		"sex" => "Geslacht",
		"insz" => "Rijksregnr",
		"diploma" => "Diploma",
		"homePhone" => "Thuistelefoon",
		"mobilePhone" => "Gsm",
		"privateEmail" => "Prive_email",
		"schoolEmail" => "School_email",
		"addressStreet" => "Straat",
		"addressNumber" => "Nr",
		"addressBus" => "Bus",
		"addressZipcode" => "Dlpostnr",
		"addressCity" => "Dlgem",
		"addressCountry" => "Landcode",
		"bankAccount" => "Iban",
		"bankId" => "Bic",
		"active" => "Actief"
	];

	public function formatFields($obj2)
	{
		$obj2->Geboortedatum = Clock::at($obj2->Geboortedatum)->format("Y-m-d");

		if ($obj2->Geslacht == 1) $obj2->Geslacht = "M";
		else if ($obj2->Geslacht == 2) $obj2->Geslacht = "F";
		else $obj2->Geslacht = "X";

		if ($obj2->Actief == "J") $obj2->Actief = 1;
		else $obj2->Actief = 0;

		// $obj2->Landcode = Mapping::get("sync/informatStaff/country/{$obj2->Landcode}");

		$obj2->Iban = CString::formatBankAccount($obj2->Iban);
		$obj2->Bic = CString::formatBankId($obj2->Bic);

		return $obj2;
	}
}
